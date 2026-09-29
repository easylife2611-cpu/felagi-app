<?php

namespace App\Services\Admin;

use App\Exceptions\SettingsVersionConflictException;
use App\Models\Setting;
use App\Models\SettingDraft;
use App\Models\SettingVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Orchestrates the Admin Change Lifecycle:
 *   draft → validate → simulate → preview → publish → verify → rollback
 *
 * Per Auth Contract 1.3 + HTTP binding 1.4 + DFM §8.3:
 *   - Publish transaction writes: settings + setting_versions + audit + outbox
 *   - Stale version → 409 SETTINGS_VERSION_CONFLICT
 *   - Rollback creates a NEW draft from older version (immutable history)
 */
class AdminChangeService
{
    public function __construct(
        private readonly AuditWriter $audit,
        private readonly OutboxWriter $outbox,
    ) {}

    /**
     * Create a typed draft for a setting key.
     */
    public function createDraft(User $actor, string $settingKey, mixed $proposedValue): SettingDraft
    {
        Setting::findOrFail($settingKey);

        return SettingDraft::create([
            'id'                  => (string) Str::uuid(),
            'setting_key'         => $settingKey,
            'proposed_value_json' => ['value' => $proposedValue],
            'proposed_by'         => $actor->id,
            'status'              => SettingDraft::STATUS_DRAFT,
        ]);
    }

    /**
     * Validate draft: type + schema + dependencies.
     * Updates draft.status → VALIDATED or REJECTED.
     */
    public function validateDraft(SettingDraft $draft): array
    {
        $setting = Setting::findOrFail($draft->setting_key);
        $report  = [
            'errors'       => [],
            'warnings'     => [],
            'validated_at' => now()->toIso8601String(),
        ];

        $value = $draft->proposed_value_json['value'] ?? null;

        // Type check
        $typeOk = match ($setting->type) {
            Setting::TYPE_BOOLEAN => is_bool($value),
            Setting::TYPE_INTEGER => is_int($value),
            Setting::TYPE_DECIMAL => is_numeric($value),
            Setting::TYPE_STRING  => is_string($value),
            Setting::TYPE_JSON    => is_array($value),
            default               => false,
        };
        if (!$typeOk) {
            $report['errors'][] = "Value does not match type {$setting->type}.";
        }

        // Schema constraints
        $schema = $setting->schema_json ?? [];
        if (isset($schema['min']) && is_numeric($value) && $value < $schema['min']) {
            $report['errors'][] = "Value below minimum {$schema['min']}.";
        }
        if (isset($schema['max']) && is_numeric($value) && $value > $schema['max']) {
            $report['errors'][] = "Value above maximum {$schema['max']}.";
        }

        // Dependency check (DFM §8.2)
        $dep = $this->checkDependencies($setting->key, $value);
        if ($dep !== null) {
            $report['errors'][] = $dep;
        }

        $draft->validation_report = $report;
        $draft->status = empty($report['errors'])
            ? SettingDraft::STATUS_VALIDATED
            : SettingDraft::STATUS_REJECTED;
        $draft->save();

        return $report;
    }

    /**
     * Simulate impact (before/after diff, no side effect).
     */
    public function simulate(SettingDraft $draft): array
    {
        $setting = Setting::findOrFail($draft->setting_key);
        $before  = $setting->value_json['value'] ?? $setting->default_json['value'] ?? null;
        $after   = $draft->proposed_value_json['value'] ?? null;

        return [
            'setting_key'            => $setting->key,
            'before'                 => $before,
            'after'                  => $after,
            'changed'                => $before !== $after,
            'risk'                   => $setting->risk,
            'requires_reauth'        => $setting->requiresReauth(),
            'requires_second_factor' => $setting->requiresSecondFactor(),
            'reversibility'          => 'new_version',
            'generated_at'           => now()->toIso8601String(),
        ];
    }

    /**
     * Preview: simulate + persist impact_preview on the draft.
     */
    public function preview(SettingDraft $draft): array
    {
        $impact = $this->simulate($draft);
        $draft->impact_preview = $impact;
        $draft->save();
        return $impact;
    }

    /**
     * Publish draft atomically.
     *
     * Transaction writes:
     *   1. setting_versions (immutable row)
     *   2. settings (current value + version_number)
     *   3. setting_drafts (status → PUBLISHED)
     *   4. audit_logs (via AuditWriter — hash chain)
     *   5. outbox_events (via OutboxWriter — aggregate_id = version UUID)
     *
     * @throws SettingsVersionConflictException if expected_version is stale
     */
    public function publish(
        User $actor,
        SettingDraft $draft,
        string $reason,
        int $expectedVersion,
        ?string $confirmationDigest = null,
    ): SettingVersion {
        $setting = Setting::findOrFail($draft->setting_key);

        if ($setting->version_number !== $expectedVersion) {
            throw new SettingsVersionConflictException(
                $setting->key,
                $expectedVersion,
                $setting->version_number,
            );
        }

        if (empty($reason) || mb_strlen($reason) < 5) {
            throw new \InvalidArgumentException('Reason is required (min 5 characters).');
        }

        $requestId = request()->attributes->get('request_id');

        return DB::transaction(function () use (
            $actor, $draft, $setting, $reason, $confirmationDigest, $requestId
        ) {
            $newVersion = $setting->version_number + 1;
            $beforeState = ['value' => $setting->value_json['value'] ?? null];
            $afterState  = ['value' => $draft->proposed_value_json['value'] ?? null];

            // 1. Immutable version row
            $version = SettingVersion::create([
                'setting_key'     => $setting->key,
                'version_number'  => $newVersion,
                'value_json'      => $draft->proposed_value_json,
                'published_by'    => $actor->id,
                'published_at'    => now(),
                'reason'          => $reason,
                'source_draft_id' => $draft->id,
            ]);

            // 2. Update current setting
            $setting->value_json     = $draft->proposed_value_json;
            $setting->version_number = $newVersion;
            $setting->updated_by     = $actor->id;
            $setting->save();

            // 3. Mark draft published
            $draft->status = SettingDraft::STATUS_PUBLISHED;
            $draft->save();

            // 4. Audit (hash chain)
            $this->audit->write(
                actor:        $actor,
                action:       'setting.publish',
                entityType:   'setting',
                entityId:     null,
                reason:       $reason,
                beforeState:  $beforeState,
                afterState:   $afterState,
                safeMetadata: [
                    'setting_key'         => $setting->key,
                    'version_number'      => $newVersion,
                    'source_draft_id'     => $draft->id,
                    'confirmation_digest' => $confirmationDigest,
                ],
                requestId:    $requestId,
            );

            // 5. Outbox (aggregate_id = version UUID)
            $this->outbox->emit(
                eventType:     'setting.published',
                aggregateType: 'setting_version',
                aggregateId:   $version->id,
                eventKey:      "setting.publish.{$setting->key}.v{$newVersion}",
                payload:       [
                    'setting_key'    => $setting->key,
                    'version_number' => $newVersion,
                    'draft_id'       => $draft->id,
                    'published_by'   => $actor->id,
                ],
            );

            return $version;
        });
    }

    /**
     * Rollback: create a NEW draft from an older published version.
     * Does NOT mutate history — the older version stays immutable.
     */
    public function rollback(User $actor, string $settingKey, int $targetVersion): SettingDraft
    {
        Setting::findOrFail($settingKey);

        $target = SettingVersion::where('setting_key', $settingKey)
            ->where('version_number', $targetVersion)
            ->firstOrFail();

        return SettingDraft::create([
            'id'                  => (string) Str::uuid(),
            'setting_key'         => $settingKey,
            'proposed_value_json' => $target->value_json,
            'proposed_by'         => $actor->id,
            'status'              => SettingDraft::STATUS_DRAFT,
            'validation_report'   => [
                'note'             => "Rollback draft from v{$targetVersion}",
                'source_version'   => $targetVersion,
                'rollback_created' => true,
            ],
        ]);
    }

    /**
     * Dependency rules per DFM §8.2.
     */
    protected function checkDependencies(string $key, mixed $value): ?string
    {
        if ($key === 'feature.boosts' && $value === true) {
            $payments = Setting::find('feature.payments');
            if (!$payments || ($payments->value_json['value'] ?? false) !== true) {
                return 'Cannot enable Boosts while Payments are OFF (feature.payments).';
            }
        }

        if ($key === 'feature.ai_compare' && $value === true) {
            $model = Setting::find('ai.model_id');
            if (!$model || empty($model->value_json['value'])) {
                return 'AI Compare requires a configured ai.model_id.';
            }
        }

        return null;
    }
}
