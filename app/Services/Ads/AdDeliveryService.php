<?php

namespace App\Services\Ads;

use App\Models\AdCampaign;
use App\Models\AdDelivery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Ad delivery resolution — LOCKED spec:
 *   System_Specification/Sponsored_Advertising_Contract.md
 *   Design_Data/advertising-contract.json
 *
 * Returns one of the canonical slot states:
 *   DISABLED | EMPTY | READY | FAILED | EXPIRED | NOT_ELIGIBLE | FREQUENCY_CAPPED
 * (LOADING is a client-side state, never returned)
 *
 * Frequency caps (from advertising-contract.json):
 *   max_per_session           3
 *   max_per_day               6
 *   max_campaign_per_session  2
 *   max_placement_per_session 2
 *   page_max                  1
 */
class AdDeliveryService
{
    public const SLOT_DISABLED          = 'DISABLED';
    public const SLOT_EMPTY             = 'EMPTY';
    public const SLOT_READY             = 'READY';
    public const SLOT_FAILED            = 'FAILED';
    public const SLOT_EXPIRED           = 'EXPIRED';
    public const SLOT_NOT_ELIGIBLE      = 'NOT_ELIGIBLE';
    public const SLOT_FREQUENCY_CAPPED  = 'FREQUENCY_CAPPED';

    // Contract version this service implements
    public const POLICY_VERSION = '1.4.0';

    /**
     * Resolve one ad slot for a given placement and session.
     *
     * @return array{state: string, delivery_id: ?string, payload: ?array, reason: ?string}
     */
    public function resolve(string $placementId, ?string $sessionRef = null): array
    {
        $sessionRef = $sessionRef ?: 'anon_' . Str::uuid();

        // 1. Placement must be registered
        if (! in_array($placementId, AdCampaign::PLACEMENTS, true)) {
            return $this->slot(self::SLOT_NOT_ELIGIBLE, null, 'placement_not_registered');
        }

        // 2. Master switch
        if (! $this->masterEnabled()) {
            return $this->slot(self::SLOT_DISABLED, null, 'master_off');
        }

        // 3. Placement switch
        if (! $this->placementEnabled($placementId)) {
            return $this->slot(self::SLOT_DISABLED, null, 'placement_off');
        }

        // 4. Frequency caps
        if ($this->isFrequencyCapped($placementId, $sessionRef)) {
            return $this->slot(self::SLOT_FREQUENCY_CAPPED, null, 'frequency_cap');
        }

        // 5. Find eligible campaigns
        $campaigns = AdCampaign::query()
            ->servable()
            ->whereNotNull('creative_id')
            ->orderByDesc('priority')
            ->orderBy('id')
            ->get();

        $eligible = [];
        foreach ($campaigns as $c) {
            if ($this->campaignTargetsPlacement($c, $placementId)) {
                $eligible[] = $c;
            }
        }

        if (empty($eligible)) {
            return $this->slot(self::SLOT_EMPTY, null, 'no_eligible_campaign');
        }

        // 6. Reserve delivery atomically
        try {
            $delivery = $this->reserve($eligible[0], $placementId, $sessionRef);
        } catch (\Throwable $e) {
            return $this->slot(self::SLOT_FAILED, null, 'reserve_failed');
        }

        // 7. Build payload
        $payload = $this->buildPayload($delivery);

        return [
            'state'       => self::SLOT_READY,
            'delivery_id' => $delivery->id,
            'payload'     => $payload,
            'reason'      => null,
        ];
    }

    /**
     * Reserve a delivery for the given campaign + placement.
     */
    private function reserve(AdCampaign $campaign, string $placementId, string $sessionRef): AdDelivery
    {
        return DB::transaction(function () use ($campaign, $placementId, $sessionRef) {
            $delivery = AdDelivery::create([
                'campaign_id'      => $campaign->id,
                'campaign_version' => $campaign->version,
                'placement_id'     => $placementId,
                'slot_id'          => 'slot_' . Str::random(12),
                'creative_version' => optional($campaign->creative)->version ?? 1,
                'policy_version'   => self::POLICY_VERSION,
                'session_ref'      => $sessionRef,
                'eligible_at'      => now(),
                'expires_at'       => now()->addMinutes(30),
            ]);

            return $delivery;
        });
    }

    /**
     * Build the payload the client renders.
     */
    private function buildPayload(AdDelivery $delivery): array
    {
        $campaign = $delivery->campaign;
        $creative = $campaign->creative;

        return [
            'delivery_id' => $delivery->id,
            'campaign_id' => $campaign->id,
            'placement_id'=> $delivery->placement_id,
            'sponsor'     => optional($campaign->advertiser)->display_name,
            'format'      => $creative?->format,
            'media_url'   => $creative?->media_asset_id,
            'copy' => [
                'am' => [
                    'title' => $creative?->copy_am_title,
                    'body'  => $creative?->copy_am_body,
                    'cta'   => $creative?->copy_am_cta,
                    'alt'   => $creative?->copy_am_alt,
                ],
                'en' => [
                    'title' => $creative?->copy_en_title,
                    'body'  => $creative?->copy_en_body,
                    'cta'   => $creative?->copy_en_cta,
                    'alt'   => $creative?->copy_en_alt,
                ],
            ],
            'destination' => [
                'type'  => $campaign->destination_type,
                'value' => $campaign->destination_value,
            ],
            'expires_at' => optional($delivery->expires_at)->toIso8601String(),
        ];
    }

    /**
     * Master switch (CACHE-able, TTL 60s per contract).
     */
    public function masterEnabled(): bool
    {
        return Cache::remember('ads.master_enabled', 60, function () {
            // Default OFF for new installations
            return false;
        });
    }

    /**
     * Per-placement switch (CACHE-able, TTL 60s).
     */
    public function placementEnabled(string $placementId): bool
    {
        return Cache::remember("ads.placement.{$placementId}.enabled", 60, function () use ($placementId) {
            // Default OFF for new installations
            return false;
        });
    }

    /**
     * Check whether the campaign targets the given placement.
     */
    private function campaignTargetsPlacement(AdCampaign $campaign, string $placementId): bool
    {
        return in_array($placementId, $campaign->placement_ids ?? [], true);
    }

    /**
     * Frequency cap check (deterministic, session-based).
     */
    private function isFrequencyCapped(string $placementId, string $sessionRef): bool
    {
        $now = now();

        $sessionCount = AdDelivery::where('session_ref', $sessionRef)
            ->where('eligible_at', '>=', $now->copy()->subMinutes(30))
            ->count();

        if ($sessionCount >= 3) {
            return true;
        }

        $dayCount = AdDelivery::where('eligible_at', '>=', $now->copy()->startOfDay())
            ->where('session_ref', $sessionRef)
            ->count();

        if ($dayCount >= 6) {
            return true;
        }

        return false;
    }

    private function slot(string $state, ?string $deliveryId, ?string $reason): array
    {
        return [
            'state'       => $state,
            'delivery_id' => $deliveryId,
            'payload'     => null,
            'reason'      => $reason,
        ];
    }
}
