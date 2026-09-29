<?php

namespace App\Services\Admin;

use App\Models\OutboxEvent;
use Illuminate\Support\Str;

/**
 * Writes outbox events with correct schema.
 *
 * Schema (outbox_events):
 *   id, event_type, aggregate_type, aggregate_id (UUID), event_key (UNIQUE),
 *   payload_json, status, attempts, available_at, locked_until,
 *   created_at, completed_at
 *
 * NOTE: No updated_at column — use created_at + completed_at only.
 */
class OutboxWriter
{
    /**
     * Emit an event to the outbox.
     *
     * @param string $eventType    e.g. "setting.published"
     * @param string $aggregateType e.g. "setting_version"
     * @param string $aggregateId  UUID of the aggregate (e.g. setting_versions.id)
     * @param string $eventKey     Idempotent unique key
     * @param array<string,mixed> $payload
     */
    public function emit(
        string $eventType,
        string $aggregateType,
        string $aggregateId,
        string $eventKey,
        array $payload,
    ): OutboxEvent {
        return OutboxEvent::create([
            'id'             => (string) Str::uuid(),
            'event_type'     => $eventType,
            'aggregate_type' => $aggregateType,
            'aggregate_id'   => $aggregateId,
            'event_key'      => $eventKey,
            'payload_json'   => $payload,
            'status'         => OutboxEvent::STATUS_PENDING,
            'attempts'       => 0,
            'available_at'   => now(),
            'created_at'     => now(),
        ]);
    }
}
