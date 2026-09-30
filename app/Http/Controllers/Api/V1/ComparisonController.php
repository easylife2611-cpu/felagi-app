<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comparison;
use App\Models\Need;
use App\Services\AI\ComparisonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComparisonController extends BaseApiController
{
    /**
     * POST /api/v1/needs/{needId}/comparisons
     * Run AI comparison via ComparisonService (WP-10).
     */
    public function store(Request $request, string $needId): JsonResponse
    {
        $need = Need::find($needId);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        if ($need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        if ($need->status !== Need::STATUS_OPEN) {
            return $this->error('STATE_CONFLICT', 'Need not OPEN.', 409);
        }

        $eligibleCount = $need->offers()->where('status', 'PENDING')->count();
        if ($eligibleCount === 0) {
            return $this->error('NO_ELIGIBLE_OFFERS', 'No eligible offers.', 422);
        }

        // Check for active comparison
        $existing = Comparison::where('need_id', $need->id)
            ->whereIn('status', [Comparison::STATUS_PENDING, Comparison::STATUS_PROCESSING])
            ->exists();
        if ($existing) {
            return $this->error('COMPARISON_PROCESSING', 'Comparison already in progress.', 409);
        }

        // WP-10: run AI comparison
        try {
            $service = app(ComparisonService::class);
            $result = $service->evaluate($need, $request->user()->id);

            return $this->success($result, 'Comparison completed.', 201);
        } catch (\RuntimeException $e) {
            return $this->error(
                'AI_COMPARISON_FAILED',
                $e->getMessage(),
                503
            );
        }
    }

    /**
     * GET /api/v1/needs/{needId}/comparisons
     */
    public function index(Request $request, string $needId): JsonResponse
    {
        $need = Need::find($needId);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        $user = $request->user();
        $isOwner = $need->requester_id === $user->id;

        if (!$isOwner) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        $comparisons = Comparison::where('need_id', $need->id)
            ->orderByDesc('version_number')
            ->get(['id', 'version_number', 'status', 'included_offer_count', 'requested_at', 'completed_at']);

        return $this->success($comparisons, 'Comparison history retrieved.');
    }

    /**
     * GET /api/v1/comparisons/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $comparison = Comparison::with(['comparisonOffers', 'results'])->find($id);
        if (!$comparison) {
            return $this->error('NOT_FOUND', 'Comparison not found.', 404);
        }

        $user = $request->user();
        $need = $comparison->need;
        $isOwner = $need && $need->requester_id === $user->id;

        $isIncludedProvider = $comparison->comparisonOffers()
            ->where('provider_id', $user->id)
            ->exists();

        if (!$isOwner && !$isIncludedProvider) {
            return $this->error('NOT_FOUND', 'Comparison not found.', 404);
        }

        return $this->success($comparison, 'Comparison retrieved.');
    }
}
