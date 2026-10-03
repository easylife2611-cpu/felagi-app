<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\OfferSubmission\IdempotencyConflictException;
use App\Exceptions\OfferSubmission\PolicyUnknownException;
use App\Models\Need;
use App\Models\OfferSubmission;
use App\Services\Offer\OfferSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OfferUnlockController extends BaseApiController
{
    public function __construct(
        private readonly OfferSubmissionService $submissions,
    ) {
    }

    /**
     * POST /api/v1/offer-submissions
     * S023 — Submit an Offer (free path or paid-path aggregate).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'need_id'            => ['required', 'uuid', 'exists:needs,id'],
            'idempotency_key'    => ['required', 'string', 'min:8', 'max:100'],
            'draft_id'           => ['required', 'uuid'],
            'draft_version'      => ['required', 'integer', 'min:1'],
            'draft_hash'         => ['required', 'string', 'size:64'],
            'offered_price'      => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'currency'           => ['nullable', 'string', 'size:3'],
            'proposal_message'   => ['required', 'string', 'min:20', 'max:10000'],
            'delivery_time_text' => ['nullable', 'string', 'max:255'],
            'availability_text'  => ['nullable', 'string', 'max:255'],
            'additional_notes'   => ['nullable', 'string', 'max:10000'],
        ]);

        $need = Need::find($validated['need_id']);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        $offerPayload = [
            'offered_price'      => $validated['offered_price'],
            'currency'           => $validated['currency'] ?? 'ETB',
            'proposal_message'   => $validated['proposal_message'],
            'delivery_time_text' => $validated['delivery_time_text'] ?? null,
            'availability_text'  => $validated['availability_text'] ?? null,
            'additional_notes'   => $validated['additional_notes'] ?? null,
        ];

        try {
            $submission = $this->submissions->submit(
                provider:       $request->user(),
                need:           $need,
                offerPayload:   $offerPayload,
                draftId:        $validated['draft_id'],
                draftVersion:   (int) $validated['draft_version'],
                draftHash:      $validated['draft_hash'],
                idempotencyKey: $validated['idempotency_key'],
            );
        } catch (PolicyUnknownException $e) {
            return $this->error('POLICY_UNKNOWN', $e->getMessage(), 503);
        } catch (IdempotencyConflictException $e) {
            return $this->error('IDEMPOTENCY_CONFLICT', $e->getMessage(), 409);
        } catch (InvalidArgumentException $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'own Need')) {
                return $this->error('FORBIDDEN', $msg, 403);
            }
            if (str_contains($msg, 'deadline')) {
                return $this->error('DEADLINE', $msg, 409);
            }
            if (str_contains($msg, 'already have an Offer')) {
                return $this->error('OFFER_EXISTS', $msg, 409);
            }
            return $this->error('STATE_CONFLICT', $msg, 409);
        }

        $status = $submission->state === OfferSubmission::STATE_SUBMITTED ? 201 : 202;

        return $this->success(
            $submission->load(['offer', 'need:id,title,status']),
            $submission->state === OfferSubmission::STATE_SUBMITTED
                ? 'Offer submitted.'
                : 'Payment required. Complete payment to unlock.',
            $status
        );
    }

    /**
     * GET /api/v1/offer-submissions/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $submission = OfferSubmission::with(['offer', 'need:id,title,status'])
            ->find($id);

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
     * Resume does NOT create a second charge (spec: submission-recovery).
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
