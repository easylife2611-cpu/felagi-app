<?php

namespace App\Services\Admin;

use App\Models\Comparison;
use App\Models\ComparisonAttempt;
use App\Models\Setting;

/**
 * Admin AI Status Service — L313 (A006)
 *
 * Real AI configuration + usage state for A006 AI screen.
 * Per WP-05c_LOCKED: endpoint = /admin/ai, controls = aiModel, aiTimeout
 * + AI evaluation controls (criteriaVersion, promptVersion, maxOffers,
 * retryLimit, dailyLimit).
 *
 * Settings keys come from ControlRegistrySeeder with documented defaults
 * (mirrors L310-L312 DB-or-default pattern). Usage stats derived from
 * the `comparisons` table.
 */
class AdminAiService
{
    public const SETTING_MODEL_ID          = 'ai.model_id';
    public const SETTING_TIMEOUT           = 'ai.timeout_seconds';
    public const SETTING_CRITERIA_VERSION  = 'ai.criteria_version';
    public const SETTING_PROMPT_VERSION    = 'ai.prompt_version';
    public const SETTING_MAX_ATTEMPTS      = 'ai.max_attempts';
    public const SETTING_USER_DAILY_CAP    = 'ai.user_daily_cap';
    public const SETTING_NEED_DAILY_CAP    = 'ai.need_daily_cap';
    public const SETTING_MAX_INPUT_TOKENS  = 'ai.max_input_tokens';
    public const SETTING_MAX_OUTPUT_TOKENS = 'ai.max_output_tokens';

    public const DEFAULT_MODEL_ID          = null;
    public const DEFAULT_TIMEOUT           = 60;
    public const DEFAULT_CRITERIA_VERSION  = 'v1';
    public const DEFAULT_PROMPT_VERSION    = 'v1';
    public const DEFAULT_MAX_ATTEMPTS      = 3;
    public const DEFAULT_USER_DAILY_CAP    = 10;
    public const DEFAULT_NEED_DAILY_CAP    = 3;
    public const DEFAULT_MAX_INPUT_TOKENS  = 4000;
    public const DEFAULT_MAX_OUTPUT_TOKENS = 1000;

    public function status(): array
    {
        return [
            'config'          => $this->config(),
            'evaluation'      => $this->evaluation(),
            'token_limits'    => $this->tokenLimits(),
            'usage_stats'     => $this->usageStats(),
            'recent_failures' => $this->recentFailures(),
            'computed_at'     => now()->toIso8601String(),
        ];
    }

    private function config(): array
    {
        $model    = Setting::find(self::SETTING_MODEL_ID);
        $timeout  = Setting::find(self::SETTING_TIMEOUT);
        $criteria = Setting::find(self::SETTING_CRITERIA_VERSION);
        $prompt   = Setting::find(self::SETTING_PROMPT_VERSION);

        return [
            'model_id'         => $model?->value_json['value']    ?? self::DEFAULT_MODEL_ID,
            'timeout_seconds'  => (int) ($timeout?->value_json['value']  ?? self::DEFAULT_TIMEOUT),
            'criteria_version' => $criteria?->value_json['value'] ?? self::DEFAULT_CRITERIA_VERSION,
            'prompt_version'   => $prompt?->value_json['value']   ?? self::DEFAULT_PROMPT_VERSION,
            'source'           => $model ? 'db' : 'default',
        ];
    }

    private function evaluation(): array
    {
        $attempts = Setting::find(self::SETTING_MAX_ATTEMPTS);
        $userCap  = Setting::find(self::SETTING_USER_DAILY_CAP);
        $needCap  = Setting::find(self::SETTING_NEED_DAILY_CAP);

        return [
            'max_attempts'   => (int) ($attempts?->value_json['value'] ?? self::DEFAULT_MAX_ATTEMPTS),
            'user_daily_cap' => (int) ($userCap?->value_json['value']  ?? self::DEFAULT_USER_DAILY_CAP),
            'need_daily_cap' => (int) ($needCap?->value_json['value']  ?? self::DEFAULT_NEED_DAILY_CAP),
        ];
    }

    private function tokenLimits(): array
    {
        $maxIn  = Setting::find(self::SETTING_MAX_INPUT_TOKENS);
        $maxOut = Setting::find(self::SETTING_MAX_OUTPUT_TOKENS);

        return [
            'max_input_tokens'  => (int) ($maxIn?->value_json['value']  ?? self::DEFAULT_MAX_INPUT_TOKENS),
            'max_output_tokens' => (int) ($maxOut?->value_json['value'] ?? self::DEFAULT_MAX_OUTPUT_TOKENS),
        ];
    }

    private function usageStats(): array
    {
        return [
            'total_comparisons' => $this->safe(fn () => Comparison::count()),
            'completed'         => $this->safe(fn () => Comparison::where('status', Comparison::STATUS_COMPLETED)->count()),
            'failed'            => $this->safe(fn () => Comparison::where('status', Comparison::STATUS_FAILED)->count()),
            'processing'        => $this->safe(fn () => Comparison::where('status', Comparison::STATUS_PROCESSING)->count()),
            'total_input_tokens'  => $this->safe(fn () => (int) Comparison::sum('input_token_count')),
            'total_output_tokens' => $this->safe(fn () => (int) Comparison::sum('output_token_count')),
            'total_cost'          => $this->safe(fn () => (float) Comparison::sum('estimated_cost')),
        ];
    }

    private function recentFailures(): array
    {
        return $this->safe(fn () => ComparisonAttempt::where('status', 'failed')
            ->orderByDesc('started_at')
            ->limit(5)
            ->get(['id', 'comparison_id', 'attempt_number', 'status', 'failure_code', 'started_at'])
            ->map(fn ($a) => [
                'id'            => $a->id,
                'comparison_id' => $a->comparison_id,
                'attempt'       => $a->attempt_number,
                'failure_code'  => $a->failure_code,
                'started_at'    => $a->started_at?->toIso8601String(),
            ])->all(), []);
    }

    private function safe(callable $fn, mixed $fallback = 0): mixed
    {
        try { return $fn(); } catch (\Throwable) { return $fallback; }
    }
}
