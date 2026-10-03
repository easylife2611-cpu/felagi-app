<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Need;
use App\Models\Offer;
use App\Models\OfferSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OfferUnlockController extends BaseApiController
{
    /**
     * POST /api/v1/offer-submissions
     * Create an offer submission (unlock flow for providers).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'need_id' => ['required', 'uuid', 'exists:needs,id'],
        ]);

        $need = Need::find($validated['need_id']);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        $user = $request->user();
        if ($need->requester_id === $user->id) {
            return $this->error('FORBIDDEN', 'Cannot unlock own Need.', 403);
        }

        if ($need->status !== Need::STATUS_OPEN) {
            return $this->error('STATE_CONFLICT', 'Need not OPEN.', 409);
        }

        $existing = OfferSubmission::query()
            ->where('need_id', $need->id)
            ->where('provider_id', $user->id)
            ->first();

        if ($existing) {
            return $this->success($existing, 'Submission already exists.');
        }

        $submission = OfferSubmission::create([
            'id' => (string) Str::uuid(),
            'need_id' => $need->id,
            'provider_id' => $user->id,
            'state'  => OfferSubmission::STATE_PENDING,
        ]);

        return $this->success($submission, 'Offer submission created. Complete payment to unlock.', 201);
    }

    /**
     * GET /api/v1/offer-submissions/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $submission = OfferSubmission::find($id);
        if (!$submission) {
            return $this->error('NOT_FOUND', 'Submission not found.', 404);
        }
        if ($submission->provider_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Not your submission.', 403);
        }

        return $this->success($submission, 'Submission retrieved.');
    }

    /**
     * POST /api/v1/offer-submissions/{id}/resume
     */
    public function resume(Request $request, string $id): JsonResponse
    {
        $submission = OfferSubmission::find($id);
        if (!$submission) {
            return $this->error('NOT_FOUND', 'Submission not found.', 404);
        }
        if ($submission->provider_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Not your submission.', 403);
        }

        return $this->success($submission, 'Submission resumed.');
    }
}
