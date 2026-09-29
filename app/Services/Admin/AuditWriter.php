<?php

namespace App\Services\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Writes append-only audit entries with SHA-256 hash chain.
 *
 * Per IMPLEMENTATION_LEDGER L071: audit_logs is append-only with hash chain.
 *
 * Hash algorithm:
 *   $hash = hash('sha256', ($prevHash ?? '') . $canonicalPayload)
 * where canonicalPayload is deterministic JSON of the row fields.
 */
class AuditWriter
{
    /**
     * Write an audit entry with hash chain integrity.
     *
     * @param string $action     e.g. "setting.publish"
     * @param string $entityType e.g. "setting"
     * @param string|null $entityId  Null for settings (key is not UUID)
     * @param array<string,mixed> $safeMetadata  e.g. ['setting_key' => ..., 'version_number' => ...]
     * @param mixed $beforeState  Any state, will be SHA-256 hashed
     * @param mixed $afterState   Any state, will be SHA-256 hashed
     */
    public function write(
        ?User $actor,
        string $action,
        string $entityType,
        ?string $entityId = null,
        ?string $reason = null,
        mixed $beforeState = null,
        mixed $afterState = null,
        array $safeMetadata = [],
        ?string $requestId = null,
    ): AuditLog {
        $occurredAt = now();
        $prevHash   = $this->getPreviousHash();

        $beforeDigest = $beforeState !== null
            ? hash('sha256', json_encode($beforeState, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
            : null;

        $afterDigest = $afterState !== null
            ? hash('sha256', json_encode($afterState, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
            : null;

        // Canonical payload for hash chain
        $canonical = json_encode([
            'actor_id'     => $actor?->id,
            'action'       => $action,
            'entity_type'  => $entityType,
            'entity_id'    => $entityId,
            'reason'       => $reason,
            'before_digest'=> $beforeDigest,
            'after_digest' => $afterDigest,
            'safe_metadata'=> $safeMetadata,
            'occurred_at'  => $occurredAt->format('Y-m-d H:i:s'),
            'request_id'   => $requestId,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $hash = hash('sha256', ($prevHash ?? '') . $canonical);

        return AuditLog::create([
            'id'             => (string) Str::uuid(),
            'actor_id'       => $actor?->id,
            'action'         => $action,
            'entity_type'    => $entityType,
            'entity_id'      => $entityId,
            'request_id'     => $requestId ?? $this->resolveRequestId(),
            'reason'         => $reason,
            'before_digest'  => $beforeDigest,
            'after_digest'   => $afterDigest,
            'safe_metadata'  => $safeMetadata,
            'occurred_at'    => $occurredAt,
            'prev_hash'      => $prevHash,
            'hash'           => $hash,
        ]);
    }

    /**
     * Retrieve the most recent audit row's hash for chain continuity.
     */
    protected function getPreviousHash(): ?string
    {
        return AuditLog::query()
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->value('hash');
    }

    protected function resolveRequestId(): string
    {
        $rid = request()->attributes->get('request_id');
        if (!$rid) {
            $rid = (string) Str::uuid();
            request()->attributes->set('request_id', $rid);
        }
        return $rid;
    }
}
