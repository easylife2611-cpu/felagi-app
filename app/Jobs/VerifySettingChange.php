<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\SettingVersion;
use App\Services\Admin\AuditWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Verify a published setting change (server-side probe).
 *
 * Design (DFM §418 + Final_Admin §44):
 *   "Verify runtime health"
 *   "Apply and verification are server jobs, never browser assertions."
 *
 * Verification states:
 *   - VERIFIED          → setting value matches latest version
 *   - REQUIRES_VERIFICATION → cannot verify (no probe available)
 *   - DRIFT             → value mismatch detected
 *
 * The job writes an audit entry with the outcome.
 * It does NOT bill users or create real marketplace records
 * (Auth Contract §3: "Verification probes must not bill users or create
 * real marketplace records").
 */
class VerifySettingChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;
    public int $timeout = 30;

    public function __construct(
        public readonly ?string $settingKey,
        public readonly ?int $versionNumber,
    ) {}

    public function handle(AuditWriter $audit): void
    {
        if (empty($this->settingKey) || $this->versionNumber === null) {
            Log::warning('VerifySettingChange: missing key or version');
            return;
        }

        $setting = Setting::find($this->settingKey);
        if (!$setting) {
            Log::warning('VerifySettingChange: setting not found', [
                'setting_key' => $this->settingKey,
            ]);
            return;
        }

        // Expected: latest version
        $latestVersion = SettingVersion::where('setting_key', $this->settingKey)
            ->where('version_number', $this->versionNumber)
            ->first();

        if (!$latestVersion) {
            Log::warning('VerifySettingChange: version not found');
            return;
        }

        // Probe: does current setting match the version we published?
        $persistedValue = $setting->value_json['value'] ?? null;
        $expectedValue  = $latestVersion->value_json['value'] ?? null;

        $state = $persistedValue === $expectedValue
            ? 'VERIFIED'
            : 'DRIFT';

        // Audit the verification outcome
        $audit->write(
            actor:        null,   // system job — no actor
            action:       'setting.verify',
            entityType:   'setting',
            entityId:     null,
            reason:       "Post-publish verification for v{$this->versionNumber}",
            beforeState:  ['value' => $expectedValue],
            afterState:   ['value' => $persistedValue],
            safeMetadata: [
                'setting_key'    => $this->settingKey,
                'version_number' => $this->versionNumber,
                'verification'   => $state,
                'verified_at'    => now()->toIso8601String(),
            ],
        );

        Log::info('Setting verified', [
            'setting_key'    => $this->settingKey,
            'version_number' => $this->versionNumber,
            'state'          => $state,
        ]);
    }
}
