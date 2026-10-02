<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comparison;
use App\Models\ComparisonFeedback;
use App\Models\ComparisonResult;
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

    /**
     * GET /api/v1/comparisons/{id}/provider-projection
     *
     * AI-21 — Provider Result Projection.
     * A provider sees ONLY their own offer's result from the immutable
     * comparison snapshot. Other providers → 403.
     */
    public function providerProjection(Request $request, string $id): JsonResponse
    {
        $comparison = Comparison::find($id);
        if (! $comparison) {
            return $this->error('NOT_FOUND', 'Comparison not found.', 404);
        }

        $providerId = $request->user()->id;

        // Find this provider's offer snapshot in the comparison
        $offer = $comparison->comparisonOffers()
            ->where('provider_id', $providerId)
            ->first();

        if (! $offer) {
            return $this->error('FORBIDDEN', 'No projection available for this provider.', 403);
        }

        $result = ComparisonResult::where('comparison_id', $comparison->id)
            ->where('comparison_offer_id', $offer->id)
            ->first();

        if (! $result) {
            return $this->error('NOT_FOUND', 'Result not yet available.', 404);
        }

        return $this->success([
            'comparison_id'    => $comparison->id,
            'version_number'   => $comparison->version_number,
            'status'           => $comparison->status,
            'offer_index'      => $comparison->comparisonOffers()
                                    ->orderBy('created_at')
                                    ->pluck('id')
                                    ->search($offer->id),
            'score'            => (float) $result->score,
            'criterion_scores' => $result->criterion_scores,
            'completeness'     => $result->completeness,
            'missing_criteria' => $result->missing_criteria,
            'uncertain_criteria' => $result->uncertain_criteria,
            'strengths'        => $result->strengths,
            'weaknesses'       => $result->weaknesses,
            'missing_information' => $result->missing_information,
            'risk_notes'       => $result->risk_notes,
            'fit_explanation'  => $result->fit_explanation,
            'projected_at'     => now()->toIso8601String(),
        ], 'Provider projection.');
    }

    /**
     * POST /api/v1/comparisons/{id}/feedback
     *
     * AI-22 — Provider Feedback UX.
     * A provider can submit one feedback per comparison. The feedback is
     * advisory only; it does not mutate the immutable result.
     */
    public function submitFeedback(Request $request, string $id): JsonResponse
    {
        $comparison = Comparison::find($id);
        if (! $comparison) {
            return $this->error('NOT_FOUND', 'Comparison not found.', 404);
        }

        $providerId = $request->user()->id;

        // Only providers who submitted an offer may leave feedback
        $hasOffer = $comparison->comparisonOffers()
            ->where('provider_id', $providerId)
            ->exists();
        if (! $hasOffer) {
            return $this->error('FORBIDDEN', 'Only providers in this comparison may leave feedback.', 403);
        }

        $data = $request->validate([
            'rating'  => ['required', 'string', 'in:' . implode(',', ComparisonFeedback::RATINGS)],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $existing = ComparisonFeedback::where('comparison_id', $comparison->id)
            ->where('provider_id', $providerId)
            ->first();
        if ($existing) {
            return $this->error(
                'FEEDBACK_ALREADY_SUBMITTED',
                'Feedback already recorded for this comparison.',
                409,
            );
        }

        $feedback = ComparisonFeedback::create([
            'comparison_id' => $comparison->id,
            'provider_id'   => $providerId,
            'rating'        => $data['rating'],
            'comment'       => $data['comment'] ?? null,
            'status'        => 'SUBMITTED',
        ]);

        return $this->success($feedback, 'Feedback recorded.', 201);
    }

    /**
     * GET /api/v1/comparisons/{id}/results
     * Requester: full projection. Provider: own result only.
     */
    public function results(Request $request, string $id): JsonResponse
    {
        $comparison = Comparison::find($id);
        if (! $comparison) {
            return $this->error('NOT_FOUND', 'Comparison not found.', 404);
        }

        $user = $request->user();
        $need = $comparison->need;
        $isOwner = $need && $need->requester_id === $user->id;

        $results = ComparisonResult::where('comparison_id', $comparison->id)->get();

        if (! $isOwner) {
            $ownOfferIds = $need
                ? $need->offers()->where('provider_id', $user->id)->pluck('id')
                : collect();
            $results = $results->whereIn('comparison_offer_id', $ownOfferIds)->values();
        }

        return $this->success([
            'comparison_id' => $comparison->id,
            'status'        => $comparison->status,
            'projection'    => $isOwner
                ? 'REQUESTER_COMPARISON_VIEW'
                : 'PROVIDER_OWN_RESULT_VIEW',
            'results'       => $results,
        ], 'Comparison results.');
    }

    /**
     * POST /api/v1/comparisons/{id}/retry
     */
    public function retry(Request $request, string $id): JsonResponse
    {
        $comparison = Comparison::find($id);
        if (! $comparison) {
            return $this->error('NOT_FOUND', 'Comparison not found.', 404);
        }

        $need = $comparison->need;
        if (! $need || $need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        if ($comparison->status !== Comparison::STATUS_FAILED) {
            return $this->error('STATE_CONFLICT', 'Only failed comparisons may retry.', 409);
        }

        try {
            $service = app(ComparisonService::class);
            $result = $service->evaluate($need, $request->user()->id);
            return $this->success($result, 'Comparison retried.', 201);
        } catch (\RuntimeException $e) {
            return $this->error('AI_COMPARISON_FAILED', $e->getMessage(), 503);
        }
    }

    /**
     * GET /api/v1/my/comparisons
     * Provider's own participation history (read-only).
     */
    public function myComparisons(Request $request): JsonResponse
    {
        $user = $request->user();

        $comparisons = Comparison::whereHas('need.offers', function ($q) use ($user) {
            $q->where('provider_id', $user->id);
        })
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'need_id', 'status', 'version_number', 'created_at']);

        return $this->success($comparisons, 'My comparison participation.');
    }

}
