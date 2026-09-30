<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\Ads\AdDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public ad delivery endpoint — no auth required.
 * Spec: GET /api/v1/ads/placements/{id}/delivery
 */
class AdsDeliveryController extends BaseApiController
{
    public function __construct(
        private readonly AdDeliveryService $service,
    ) {}

    public function show(Request $request, string $placementId): JsonResponse
    {
        // Use session id from cookie/header if present, else generate one
        $sessionRef = $request->header('X-Ad-Session')
            ?? $request->cookie('ad_session')
            ?? null;

        $result = $this->service->resolve($placementId, $sessionRef);

        return $this->success(
            data: [
                'slot_state'  => $result['state'],
                'delivery_id' => $result['delivery_id'],
                'payload'     => $result['payload'],
                'reason'      => $result['reason'],
            ],
            message: 'Ad delivery resolved.',
        );
    }
}
