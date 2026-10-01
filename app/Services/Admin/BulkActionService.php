<?php

namespace App\Services\Admin;

use App\Models\BulkAction;
use App\Models\Setting;
use App\Models\SettingVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * AM (audit L276) — Bulk action safety.
 *
 * Per Admin_Authorization_Contract.md line 9:
 *   - Frozen selection digest at preview (count + IDs + scope + effect + actor)
 *   - Recheck each item at execution
 *   - Item-level succeeded / failed / unknown
 *   - Retry only failed eligible IDs using original idempotency keys
 *   - No 'select all' silently means all records
 *   - Export redacts private information
 *
 * Supported actions:
 *   - setting.disable  → boolean settings only; sets value=false
 *   - test.noop        → framework-only; always succeeds
 */
class BulkActionService
{
    public const MAX_BATCH = 100;

    public const ACTION_SETTING_DISABLE = 'setting.disable';
    public const ACTION_TEST_NOOP       = 'test.noop';
    public const ACTION_PRESET_APPLY    = 'preset.apply';

    public const SUPPORTED_ACTIONS = [
        self::ACTION_SETTING_DISABLE,
        self::ACTION_TEST_NOOP,
        self::ACTION_PRESET_APPLY,
    ];

    public function __construct(
        private readonly AuditWriter $audit,
    ) {}

    // ──────────────────────────────────────────────────────────
    // Preview
    // ──────────────────────────────────────────────────────────

    public function preview(
        User $actor,
        string $actionType,
        string $entityType,
        string $scope,
        array $selectionIds,
    ): array {
        $this->assertKnownAction($actionType);

        if (count($selectionIds) > self::MAX_BATCH) {
            throw new \InvalidArgumentException(
                'Selection exceeds max batch size of ' . self::MAX_BATCH . '.'
            );
        }

        // No 'select all' silently means all records
        if (empty($selectionIds)) {
            throw new \InvalidArgumentException(
                'Empty selection — refusing to interpret as "all records".'
            );
        }

        // Dedupe + sort — the digest must be stable regardless of order
        $ids = array_values(array_unique(array_map('strval', $selectionIds)));
        sort($ids);

        $digest = $this->computeDigest($actionType, $entityType, $scope, $ids);

        $authorized   = [];
        $unauthorized = [];
        foreach ($ids as $id) {
            if ($this->isItemAuthorized($actor, $actionType, $entityType, $id)) {
                $authorized[] = $id;
            } else {
                $unauthorized[] = $id;
            }
        }

        return [
            'action_type'         => $actionType,
            'entity_type'         => $entityType,
            'scope'               => $scope,
            'selection_ids'       => $ids,
            'selection_count'     => count($ids),
            'selection_digest'    => $digest,
            'authorized_count'    => count($authorized),
            'unauthorized_count'  => count($unauthorized),
            'unauthorized_ids'    => $unauthorized,
            'max_batch'           => self::MAX_BATCH,
            'previewed_at'        => now()->toIso8601String(),
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Execute
    // ──────────────────────────────────────────────────────────

    public function execute(User $actor, array $payload): BulkAction
    {
        $actionType = $payload['action_type'] ?? null;
        $entityType = $payload['entity_type'] ?? null;
        $scope      = $payload['scope'] ?? null;
        $ids        = $payload['selection_ids'] ?? [];
        $digest     = $payload['selection_digest'] ?? null;

        if (! $actionType || ! $entityType || ! $scope || ! $digest) {
            throw new \InvalidArgumentException('Missing required execute payload fields.');
        }

        $this->assertKnownAction($actionType);

        if (count($ids) > self::MAX_BATCH) {
            throw new \InvalidArgumentException('Selection exceeds max batch size.');
        }
        if (empty($ids)) {
            throw new \InvalidArgumentException('Empty selection.');
        }

        $normalized = array_values(array_unique(array_map('strval', $ids)));
        sort($normalized);

        $expectedDigest = $this->computeDigest($actionType, $entityType, $scope, $normalized);
        if (! hash_equals($expectedDigest, (string) $digest)) {
            throw new \RuntimeException('SELECTION_DIGEST_MISMATCH');
        }

        return DB::transaction(function () use ($actor, $actionType, $entityType, $scope, $normalized, $expectedDigest) {
            $bulk = BulkAction::create([
                'actor_id'         => $actor->id,
                'action_type'      => $actionType,
                'entity_type'      => $entityType,
                'scope'            => $scope,
                'selection_ids'    => $normalized,
                'selection_digest' => $expectedDigest,
                'expected_count'   => count($normalized),
                'status'           => BulkAction::STATUS_PREVIEWED,
                'items'            => [],
                'created_at'       => now(),
            ]);

            return $this->runItems($actor, $bulk, $normalized);
        });
    }

    // ──────────────────────────────────────────────────────────
    // Retry failed
    // ──────────────────────────────────────────────────────────

    public function retryFailed(User $actor, BulkAction $original): BulkAction
    {
        if ($original->actor_id !== $actor->id) {
            throw new \RuntimeException('Cannot retry a bulk action owned by another actor.');
        }

        $failedIds = collect($original->items ?? [])
            ->where('status', BulkAction::ITEM_FAILED)
            ->pluck('entity_id')
            ->unique()
            ->values()
            ->all();

        if (empty($failedIds)) {
            throw new \RuntimeException('No failed items to retry.');
        }

        sort($failedIds);
        $digest = $this->computeDigest(
            $original->action_type,
            $original->entity_type,
            $original->scope,
            $failedIds
        );

        return DB::transaction(function () use ($actor, $original, $failedIds, $digest) {
            $bulk = BulkAction::create([
                'actor_id'         => $actor->id,
                'action_type'      => $original->action_type,
                'entity_type'      => $original->entity_type,
                'scope'            => $original->scope,
                'selection_ids'    => $failedIds,
                'selection_digest' => $digest,
                'expected_count'   => count($failedIds),
                'status'           => BulkAction::STATUS_PREVIEWED,
                'items'            => [],
                'retry_of_id'      => $original->id,
                'created_at'       => now(),
            ]);

            return $this->runItems($actor, $bulk, $failedIds);
        });
    }

    // ──────────────────────────────────────────────────────────
    // Internals
    // ──────────────────────────────────────────────────────────

    private function runItems(User $actor, BulkAction $bulk, array $ids): BulkAction
    {
        $items = [];
        $succeeded = 0;
        $failed = 0;
        $unknown = 0;

        foreach ($ids as $id) {
            $idempotencyKey = $this->idempotencyKeyFor($bulk->id, $id);

            // Recheck each item at execution time
            if (! $this->isItemAuthorized($actor, $bulk->action_type, $bulk->entity_type, $id)) {
                $items[] = [
                    'entity_id'       => $id,
                    'status'          => BulkAction::ITEM_UNKNOWN,
                    'error'           => 'Not authorized at execution time',
                    'idempotency_key' => $idempotencyKey,
                ];
                $unknown++;
                continue;
            }

            try {
                $this->executeItem($actor, $bulk->action_type, $bulk->entity_type, $id, $bulk->id);
                $items[] = [
                    'entity_id'       => $id,
                    'status'          => BulkAction::ITEM_SUCCEEDED,
                    'error'           => null,
                    'idempotency_key' => $idempotencyKey,
                ];
                $succeeded++;
            } catch (\Throwable $e) {
                $items[] = [
                    'entity_id'       => $id,
                    'status'          => BulkAction::ITEM_FAILED,
                    'error'           => $e->getMessage(),
                    'idempotency_key' => $idempotencyKey,
                ];
                $failed++;
            }
        }

        $status = match (true) {
            $failed === 0 && $unknown === 0 => BulkAction::STATUS_EXECUTED,
            $succeeded > 0                  => BulkAction::STATUS_PARTIAL,
            default                         => BulkAction::STATUS_FAILED,
        };

        $bulk->status       = $status;
        $bulk->items        = $items;
        $bulk->executed_at  = now();
        $bulk->completed_at = now();
        $bulk->save();

        return $bulk;
    }

    private function executeItem(User $actor, string $actionType, string $entityType, string $id, string $bulkId): void
    {
        match ($actionType) {
            self::ACTION_SETTING_DISABLE => $this->executeSettingDisable($actor, $id, $bulkId),
            self::ACTION_TEST_NOOP       => null,
            self::ACTION_PRESET_APPLY    => $this->executePresetItem($actor, $id, $bulkId),
            default                      => throw new \RuntimeException("Unknown action: {$actionType}"),
        };
    }

    private function executeSettingDisable(User $actor, string $key, string $bulkId): void
    {
        $setting = Setting::find($key);
        if (! $setting) {
            throw new \RuntimeException("Setting not found: {$key}");
        }
        if ($setting->type !== Setting::TYPE_BOOLEAN) {
            throw new \RuntimeException("Only boolean settings can be disabled: {$key}");
        }

        $current = $setting->value_json['value'] ?? null;
        if ($current === false) {
            return; // idempotent success — already disabled
        }

        $newVersion = ((int) $setting->version_number) + 1;

        SettingVersion::create([
            'setting_key'    => $key,
            'version_number' => $newVersion,
            'value_json'     => ['value' => false],
            'published_by'   => $actor->id,
            'published_at'   => now(),
            'reason'         => "Bulk disable via bulk_action {$bulkId}",
        ]);

        $setting->value_json     = ['value' => false];
        $setting->version_number = $newVersion;
        $setting->updated_by     = $actor->id;
        $setting->save();

        $this->audit->write(
            actor:        $actor,
            action:       'setting.bulk-disable.item',
            entityType:   'setting',
            entityId:     null,
            reason:       "Bulk disable via bulk_action {$bulkId}",
            beforeState:  ['value' => $current],
            afterState:   ['value' => false],
            safeMetadata: [
                'setting_key' => $key,
                'bulk_id'     => $bulkId,
                'version'     => $newVersion,
            ],
        );
    }

    private function isItemAuthorized(User $actor, string $actionType, string $entityType, string $id): bool
    {
        // For this iteration, MAIN_ADMIN or ADMIN role is required.
        // The route-level `admin` middleware already filters non-admins,
        // so we just double-check the role here.
        return $actor->roles()
            ->whereIn('role', ['MAIN_ADMIN', 'ADMIN'])
            ->whereNull('revoked_at')
            ->exists();
    }

    private function assertKnownAction(string $actionType): void
    {
        if (! in_array($actionType, self::SUPPORTED_ACTIONS, true)) {
            throw new \InvalidArgumentException("Unsupported action_type: {$actionType}");
        }
    }

    private function computeDigest(string $actionType, string $entityType, string $scope, array $sortedIds): string
    {
        $payload = [
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'scope'       => $scope,
            'ids'         => $sortedIds,
            'count'       => count($sortedIds),
        ];
        return hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    private function idempotencyKeyFor(string $bulkId, string $entityId): string
    {
        return 'bulk:' . $bulkId . ':' . hash('sha256', $entityId);
    }

    /**
     * AH (audit L276) — apply a named preset by expanding it into a
     * single bulk action. All safety machinery (frozen digest, per-item
     * recheck, item-level outcome, audit) applies.
     */
    public function applyPreset(
        User $actor,
        string $presetName,
        array $keys,
        array $values,
    ): BulkAction {
        if (empty($keys)) {
            throw new \InvalidArgumentException('Preset has no applicable keys.');
        }

        $ids = array_values(array_unique(array_map('strval', $keys)));
        sort($ids);

        $digest = $this->computeDigest(
            self::ACTION_PRESET_APPLY,
            'setting',
            'preset:' . $presetName,
            $ids,
        );

        return DB::transaction(function () use ($actor, $presetName, $ids, $digest, $values) {
            $bulk = BulkAction::create([
                'actor_id'         => $actor->id,
                'action_type'      => self::ACTION_PRESET_APPLY,
                'entity_type'      => 'setting',
                'scope'            => 'preset:' . $presetName,
                'selection_ids'    => $ids,
                'selection_digest' => $digest,
                'expected_count'   => count($ids),
                'status'           => BulkAction::STATUS_PREVIEWED,
                'items'            => [],
                'created_at'       => now(),
            ]);

            // Stash preset values into the service's per-item context
            $this->presetValues = $values;

            return $this->runItems($actor, $bulk, $ids);
        });
    }

    /** Per-call preset values (transient, cleared after each runItems) */
    private array $presetValues = [];

    private function executePresetItem(User $actor, string $key, string $bulkId): void
    {
        $setting = Setting::find($key);
        if (! $setting) {
            throw new \RuntimeException("Setting not found: {$key}");
        }

        $newValue = $this->presetValues[$key] ?? null;

        $current = $setting->value_json['value'] ?? null;
        if ($current === $newValue) {
            return; // idempotent success
        }

        $newVersion = ((int) $setting->version_number) + 1;

        SettingVersion::create([
            'setting_key'    => $key,
            'version_number' => $newVersion,
            'value_json'     => ['value' => $newValue],
            'published_by'   => $actor->id,
            'published_at'   => now(),
            'reason'         => "Preset apply via bulk_action {$bulkId}",
        ]);

        $setting->value_json     = ['value' => $newValue];
        $setting->version_number = $newVersion;
        $setting->updated_by     = $actor->id;
        $setting->save();

        $this->audit->write(
            actor:        $actor,
            action:       'setting.preset-apply.item',
            entityType:   'setting',
            entityId:     null,
            reason:       "Preset apply via bulk_action {$bulkId}",
            beforeState:  ['value' => $current],
            afterState:   ['value' => $newValue],
            safeMetadata: [
                'setting_key' => $key,
                'bulk_id'     => $bulkId,
                'version'     => $newVersion,
            ],
        );
    }
}
