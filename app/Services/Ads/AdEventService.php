<?php

namespace App\Services\Ads;

use App\Models\AdDelivery;
use App\Models\AdEvent;
use Illuminate\Support\Facades\DB;

/**
 * Ad event ingestion — LOCKED spec:
 *   advertising-contract.json.analytics
 *
 * Rules:
 *   - Impression requires ≥50% visible ≥1s while foreground
 *   - Click is explicit activation, not purchase
 *   - Dedup key = event_id + delivery_id + type
 *   - Reconcile offline events with original observed time
 */
class AdEventService
{
    /**
     * Record an impression or click event.
     *
     * @param  array{event_id?:string,delivery_id:string,type:string,observed_at?:string,coverage?:float}  $input
     * @return array{accepted:bool, outcome:string, event_id:?string, reason:?string}
     */
    public function record(array $input): array
    {
        $type       = strtoupper($input['type'] ?? '');
        $deliveryId = $input['delivery_id'] ?? null;

        if (! in_array($type, AdEvent::TYPES, true)) {
            return $this->reject('invalid_event_type');
        }

        if (empty($deliveryId)) {
            return $this->reject('missing_delivery_id');
        }

        $delivery = AdDelivery::find($deliveryId);
        if (! $delivery) {
            return $this->reject('delivery_not_found');
        }

        // Expired delivery
        if ($delivery->expires_at && $delivery->expires_at->isPast()) {
            return $this->reject('delivery_expired');
        }

        // Coverage requirement for impressions
        $coverage = $input['coverage'] ?? null;
        if ($type === AdEvent::TYPE_IMPRESSION && ($coverage === null || $coverage < 0.5)) {
            return $this->reject('insufficient_coverage');
        }

        // Dedupe key
        $eventId   = $input['event_id'] ?? ('evt_' . bin2hex(random_bytes(12)));
        $dedupeKey = $eventId . '_' . $deliveryId . '_' . $type;

        // Check for existing
        $existing = AdEvent::where('dedupe_key', $dedupeKey)->first();
        if ($existing) {
            return [
                'accepted' => true,
                'outcome'  => 'DUPLICATE',
                'event_id' => $existing->id,
                'reason'   => null,
            ];
        }

        try {
            $event = DB::transaction(function () use ($eventId, $deliveryId, $type, $input, $dedupeKey, $coverage) {
                return AdEvent::create([
                    'event_id'          => $eventId,
                    'delivery_id'       => $deliveryId,
                    'type'              => $type,
                    'observed_at'       => $input['observed_at'] ?? now(),
                    'coverage'          => $coverage,
                    'dedupe_key'        => $dedupeKey,
                    'ingestion_outcome' => 'ACCEPTED',
                ]);
            });

            return [
                'accepted' => true,
                'outcome'  => 'ACCEPTED',
                'event_id' => $event->id,
                'reason'   => null,
            ];
        } catch (\Throwable $e) {
            return $this->reject('storage_failure');
        }
    }

    private function reject(string $reason): array
    {
        return [
            'accepted' => false,
            'outcome'  => 'REJECTED',
            'event_id' => null,
            'reason'   => $reason,
        ];
    }

    /**
     * Aggregate campaign report metrics.
     */
    public function campaignReport(AdDelivery|string $campaignId): array
    {
        $campaignId = $campaignId instanceof AdDelivery ? $campaignId->campaign_id : $campaignId;

        $impressions = AdEvent::query()
            ->whereHas('delivery', fn ($q) => $q->where('campaign_id', $campaignId))
            ->where('type', AdEvent::TYPE_IMPRESSION)
            ->count();

        $clicks = AdEvent::query()
            ->whereHas('delivery', fn ($q) => $q->where('campaign_id', $campaignId))
            ->where('type', AdEvent::TYPE_CLICK)
            ->count();

        $ctr = $impressions > 0 ? round($clicks / $impressions, 4) : null;

        return [
            'impressions' => $impressions,
            'clicks'      => $clicks,
            'ctr'         => $ctr,
            'source'      => 'internal_events',
            'coverage'    => $impressions > 0 ? 'PARTIAL' : 'NO_DATA',
        ];
    }
}
