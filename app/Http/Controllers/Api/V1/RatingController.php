<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Need;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RatingController extends BaseApiController
{
    /**
     * POST /api/v1/needs/{needId}/ratings
     */
    public function store(Request $request, string $needId): JsonResponse
    {
        $need = Need::with('award')->find($needId);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        if ($need->status !== Need::STATUS_COMPLETED) {
            return $this->error('NOT_COMPLETED', 'Need must be COMPLETED.', 422);
        }

        if (!$need->award) {
            return $this->error('NO_AWARD', 'Need has no award.', 422);
        }

        $validated = $request->validate([
            'to_user_id' => ['required', 'uuid', 'exists:users,id'],
            'score' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:10000'],
        ]);

        $user = $request->user();
        $acceptedProviderId = $need->award->offer()->value('provider_id');

        // Only participants can rate
        $isRequester = $need->requester_id === $user->id;
        $isProvider = $acceptedProviderId === $user->id;

        if (!$isRequester && !$isProvider) {
            return $this->error('FORBIDDEN', 'Not a participant.', 403);
        }

        // Verify target is the other party
        if ($isRequester && $validated['to_user_id'] !== $acceptedProviderId) {
            return $this->error('INVALID_TARGET', 'Must rate the accepted provider.', 422);
        }
        if ($isProvider && $validated['to_user_id'] !== $need->requester_id) {
            return $this->error('INVALID_TARGET', 'Must rate the requester.', 422);
        }

        if ($validated['to_user_id'] === $user->id) {
            return $this->error('SELF_RATING', 'Cannot rate yourself.', 422);
        }

        if (Rating::where('need_id', $need->id)
            ->where('from_user_id', $user->id)
            ->where('to_user_id', $validated['to_user_id'])
            ->exists()) {
            return $this->error('RATING_EXISTS', 'Already rated.', 409);
        }

        $rating = DB::transaction(function () use ($need, $user, $validated) {
            $rating = Rating::create([
                'id' => (string) Str::uuid(),
                'need_id' => $need->id,
                'from_user_id' => $user->id,
                'to_user_id' => $validated['to_user_id'],
                'score' => $validated['score'],
                'review' => $validated['review'] ?? null,
                'created_at' => now(),
            ]);

            // Recompute recipient's rating stats
            $stats = Rating::where('to_user_id', $validated['to_user_id'])
                ->selectRaw('COUNT(*) as cnt, AVG(score) as avg_score')
                ->first();

            DB::table('users')
                ->where('id', $validated['to_user_id'])
                ->update([
                    'rating_count' => $stats->cnt,
                    'rating_score' => $stats->avg_score,
                ]);

            return $rating;
        });

        return $this->success($rating->fresh(), 'Rating submitted.', 201);
    }
}
