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
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
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
