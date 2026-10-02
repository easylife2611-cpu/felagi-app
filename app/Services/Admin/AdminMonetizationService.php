<?php

namespace App\Services\Admin;

use App\Models\Boost;
use App\Models\BoostPackage;
use App\Models\Payment;
use App\Models\Setting;

/**
 * Admin Monetization Status Service — L311 (A020)
 *
 * Real monetization state for A020 Monetization screen.
 * Per WP-05c_LOCKED: columns = status, version, updated.
 * Controls: boost, offerUnlock, offerFee, offerCurrency, boostPackages.
 *
 * Settings keys come from ControlRegistrySeeder + control-registry.json
 * production_defaults. When DB is empty, returns documented defaults
 * with source='default' (mirrors L310 AdminFeaturesService).
 */
class AdminMonetizationService
{
    public const SETTING_BOOST         = 'feature.boosts';
    public const SETTING_PAYMENTS      = 'feature.payments';
    public const SETTING_OFFER_UNLOCK  = 'offer.unlock_enabled';
    public const SETTING_OFFER_FEE     = 'offer.fee';
    public const SETTING_OFFER_CURRENCY = 'offer.currency';

    public const DEFAULT_OFFER_UNLOCK   = false;
    public const DEFAULT_OFFER_FEE      = 0;
    public const DEFAULT_OFFER_CURRENCY = 'ETB';

    public function status(): array
    {
        return [
            'master'        => $this->masterState(),
            'offer_unlock'  => $this->offerUnlockState(),
            'stats'         => $this->stats(),
            'packages'      => $this->packages(),
            'computed_at'   => now()->toIso8601String(),
        ];
    }

    private function masterState(): array
    {
        $boosts   = Setting::find(self::SETTING_BOOST);
        $payments = Setting::find(self::SETTING_PAYMENTS);

        return [
            'boost_enabled'    => (bool) ($boosts?->value_json['value'] ?? false),
            'payments_enabled' => (bool) ($payments?->value_json['value'] ?? false),
            'source'           => $boosts ? 'db' : 'default',
        ];
    }

    private function offerUnlockState(): array
    {
        $unlock   = Setting::find(self::SETTING_OFFER_UNLOCK);
        $fee      = Setting::find(self::SETTING_OFFER_FEE);
        $currency = Setting::find(self::SETTING_OFFER_CURRENCY);

        return [
            'enabled'   => (bool) ($unlock?->value_json['value'] ?? self::DEFAULT_OFFER_UNLOCK),
            'fee'       => $fee?->value_json['value'] ?? self::DEFAULT_OFFER_FEE,
            'currency'  => $currency?->value_json['value'] ?? self::DEFAULT_OFFER_CURRENCY,
            'source'    => $unlock ? 'db' : 'default',
        ];
    }

    private function stats(): array
    {
        return [
            'total_packages'  => $this->safe(fn () => BoostPackage::count()),
            'active_packages' => $this->safe(fn () => BoostPackage::where('active', true)->count()),
            'active_boosts'   => $this->safe(fn () => Boost::where('status', Boost::STATUS_ACTIVE)->count()),
            'total_unlocks'   => $this->safe(fn () => Payment::where('purpose', Payment::PURPOSE_OFFER_UNLOCK)
                                                          ->where('status', Payment::STATUS_CONFIRMED)->count()),
            'total_revenue'   => $this->safe(fn () => (float) Payment::where('status', Payment::STATUS_CONFIRMED)->sum('amount')),
        ];
    }

    private function packages(): array
    {
        return $this->safe(fn () => BoostPackage::orderBy('duration_days')
            ->get(['id', 'duration_days', 'price', 'currency', 'active'])
            ->map(fn ($p) => [
                'id'            => $p->id,
                'duration_days' => $p->duration_days,
                'price'         => $p->price,
                'currency'      => $p->currency,
                'active'        => (bool) $p->active,
            ])->all(), []);
    }

    private function safe(callable $fn, mixed $fallback = 0): mixed
    {
        try { return $fn(); } catch (\Throwable) { return $fallback; }
    }
}
