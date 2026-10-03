<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Driver
    |--------------------------------------------------------------------------
    | null   — deterministic in-process mock (no network, no creds)
    | stripe — live Stripe (requires STRIPE_SECRET)
    */
    'driver' => env('PAYMENTS_DRIVER', 'null'),

    'currency' => env('PAYMENTS_CURRENCY', 'ETB'),

    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
];
