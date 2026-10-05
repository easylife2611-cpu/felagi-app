<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Need\UnderstandNeedRequest;
use App\Services\AI\NeedUnderstandingService;
use Illuminate\Http\JsonResponse;

final class NeedAiController extends BaseApiController
{
    public function __construct(private readonly NeedUnderstandingService $service) {}

    public function understand(UnderstandNeedRequest $request): JsonResponse
    {
        try {
            return $this->success($this->service->understand($request->validated()), 'Need draft understood.');
        } catch (\RuntimeException $e) {
            return $this->error('AI_NEED_UNDERSTANDING_FAILED', $e->getMessage(), 503);
        }
    }

    public function clarify(UnderstandNeedRequest $request): JsonResponse
    {
        return $this->understand($request);
    }

    public function prepare(UnderstandNeedRequest $request): JsonResponse
    {
        return $this->understand($request);
    }
}
