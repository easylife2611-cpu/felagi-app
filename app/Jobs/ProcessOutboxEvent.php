<?php

namespace App\Jobs;

use App\Models\OutboxEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process a single outbox event.
 *
 * Design (DFM §147 + §461):
 *   "outbox_events... Created in the same transaction as the business state
 *    it describes; dispatch after commit. Consumers are idempotent."
 *   "The scheduler invokes bounded, short-lived queue workers..."
 *
 * Idempotent: Uses `locked_until` column with 5-minute lock to avoid
 * duplicate processing on worker retries.
 *
 * Event types handled:
 *   - setting.published → triggers VerifySettingChange
 *   - (future) payment.confirmed, etc.
 */
class ProcessOutboxEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;
    public int $timeout = 45;

    public function __construct(
        public readonly string $outboxEventId,
    ) {}

    public function handle(): void
    {
        // Lock + load the event
        $event = OutboxEvent::query()
            ->where('id', $this->outboxEventId)
            ->where(function ($q) {
                $q->whereNull('locked_until')
                  ->orWhere('locked_until', '<', now());
            })
            ->whereIn('status', [
                OutboxEvent::STATUS_PENDING,
                OutboxEvent::STATUS_FAILED,
            ])
            ->first();

        // Already processed or locked by another worker
        if (!$event) {
            Log::info('Outbox event skipped', ['id' => $this->outboxEventId]);
            return;
        }

        // Mark PROCESSING
        $event->status = OutboxEvent::STATUS_PROCESSING;
        $event->attempts = $event->attempts + 1;
        $event->locked_until = now()->addMinutes(5);
        $event->save();

        try {
            $this->dispatch($event);

            $event->markDone();
            Log::info('Outbox event processed', [
                'id'         => $event->id,
                'event_type' => $event->event_type,
            ]);
        } catch (\Throwable $e) {
            $event->status = OutboxEvent::STATUS_FAILED;
            $event->locked_until = null;
            $event->save();

            Log::error('Outbox event failed', [
                'id'    => $event->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Route the event to its handler.
     */
    protected function dispatch(OutboxEvent $event): void
    {
        match ($event->event_type) {
            'setting.published' => VerifySettingChange::dispatch(
                $event->payload_json['setting_key'] ?? null,
                $event->payload_json['version_number'] ?? null,
            ),
            default => Log::warning('Unknown outbox event type', [
                'event_type' => $event->event_type,
                'id'         => $event->id,
            ]),
        };
    }
}
