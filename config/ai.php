<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    | Supported: "gemini", "openai", "null"
    */
    'provider' => env('AI_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | Gemini (Google AI) Configuration
    |--------------------------------------------------------------------------
    | Uses the Gemini Developer API (generativelanguage.googleapis.com).
    | Set GEMINI_API_KEY in .env.
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'model' => env('GEMINI_MODEL', 'gemini-flash-lite-latest'),

        /*
        | Fallback models — tried in order when the primary returns a
        | transient error (HTTP 503 UNAVAILABLE) or a deprecated-model
        | error (HTTP 404 NOT_FOUND).
        |
        | Verified working on 2026-10-02 (G06B live probe):
        |   - gemini-flash-lite-latest  → 200 (resolves to gemini-3.5-flash-lite)
        |   - gemini-3-flash-preview    → 200
        |
        | Primary gemini-flash-latest was observed returning 503 during
        | demand spikes (documented in L287 and L318 evidence).
        */
        'fallback_models' => [
            env('GEMINI_FALLBACK_1', 'gemini-flash-lite-latest'),
            env('GEMINI_FALLBACK_2', 'gemini-3-flash-preview'),
        ],

        'timeout' => (int) env('GEMINI_TIMEOUT', 60),
        'max_retries' => (int) env('GEMINI_MAX_RETRIES', 3),
        'retry_delay_ms' => (int) env('GEMINI_RETRY_DELAY_MS', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Comparison Settings
    |--------------------------------------------------------------------------
    */
    'comparison' => [
        // Max offers per comparison
        'max_offers' => (int) env('AI_MAX_OFFERS', 20),
        // 4 canonical criteria (from AI_Evaluation_Contract.md)
        'criteria' => [
            'price' => ['weight' => 0.30],
            'delivery_time' => ['weight' => 0.25],
            'quality' => ['weight' => 0.30],
            'reliability' => ['weight' => 0.15],
        ],
        // Score range
        'score_min' => 0,
        'score_max' => 100,
    ],
];
