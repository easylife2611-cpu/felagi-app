<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\Ads\AdEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public ad event ingestion — no auth required.
 * Spec: POST /api/v1/ads/events
 */
class AdsEventController extends BaseApiController
{
    public function __construct(
        private readonly AdEventService $service,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id'    => ['nullable', 'string', 'max:64'],
            'delivery_id' => ['required', 'uuid'],
            'type'        => ['required', 'string', 'in:IMPRESSION,CLICK,impression,click'],
            'observed_at' => ['nullable', 'date'],
            'coverage'    => ['nullable', 'numeric', 'between:0,1'],
        ]);

        $result = $this->service->record($validated);

        if (! $result['accepted']) {
            return $this->error(
                'EVENT_REJECTED',
                $result['reason'] ?? 'Event rejected.',
                422
            );
        }

        return $this->success(
            data: ['outcome' => $result['outcome']],
            message: 'Event accepted.',
            status: 202
        );
    }
}
