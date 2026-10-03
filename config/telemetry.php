<?php

return [
    'enabled' => env('TELEMETRY_ENABLED', true),

    // "null" | "log" | "file"
    'driver' => env('TELEMETRY_DRIVER', 'null'),

    'drivers' => [
        'log' => [
            'channel' => env('TELEMETRY_LOG_CHANNEL', 'single'),
        ],
        'file' => [
            'path' => env('TELEMETRY_FILE_PATH', storage_path('logs/telemetry.log')),
        ],
    ],

    'tags' => [
        'app' => env('APP_NAME', 'Felagi'),
        'env' => env('APP_ENV', 'production'),
    ],
];
