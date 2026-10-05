<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Driver
    |--------------------------------------------------------------------------
    | null   — deterministic in-process mock (no network, no creds)
    | stripe — live Stripe (requires STRIPE_SECRET)
    */
    'driver' => env('PAYMENTS_DRIVER', env('PAYMENT_DRIVER', 'null')),

    'currency' => env('PAYMENTS_CURRENCY', 'ETB'),

    /*
    |--------------------------------------------------------------------------
    | S023 — Offer Submission Unlock Policy
    |--------------------------------------------------------------------------
    | Per Monetization_Payment_Specification.md:
    |   OFF = FREE
    |   ON + 0 ETB = FREE
    |   ON + positive amount = PAYMENT REQUIRED
    |
    | Production default: feature_enabled=false; amount_minor=0; currency=ETB.
    | Missing/partial policy → POLICY_UNKNOWN (blocks submission).
    */
    'unlock' => [
        'feature_enabled' => env('OFFER_UNLOCK_ENABLED', false),
        'amount_minor'    => env('OFFER_UNLOCK_AMOUNT_MINOR', 0),
        'policy_version'  => env('OFFER_UNLOCK_POLICY_VERSION', '1.3'),
    ],

    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
];