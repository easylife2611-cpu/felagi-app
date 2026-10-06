<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;

/**
 * Public runtime config for mobile clients.
 *
 * Allows APK to fetch dynamic values at startup without rebuild.
 * See DFM §remote-config for details.
 */
class ConfigController extends BaseApiController
{
    /**
     * GET /api/v1/config
     *
     * Returns public config values used by mobile at runtime.
     */
    public function show(): JsonResponse
    {
        return $this->success([
            'telegram_return_uri' => config(
                'app.telegram_return_uri',
                'https://zagcreativity.com/auth/mobile-handoff'
            ),
            'app_version'         => config('app.version', '1.4.3'),
            'min_supported_version' => config('app.min_supported_version', '1.4.0'),
            'features'            => [
                'offers'     => true,
                'comparison' => true,
                'boost'      => true,
            ],
            'texts' => [
                // Reserved for future dynamic text overrides
            ],
        ], 'Config retrieved.');
    }
}
