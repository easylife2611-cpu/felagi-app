<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Offer\StoreOfferRequest;
use App\Http\Requests\Offer\UpdateOfferRequest;
use App\Models\Need;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OfferController extends BaseApiController
{
    /**
     * GET /api/v1/needs/{needId}/offers
     * List offers on own Need (requester view).
     */
    public function index(Request $request, string $needId): JsonResponse
    {
        $need = Need::find($needId);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        if ($need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        $offers = $need->offers()
            ->with(['provider:id,full_name,profile_photo_url,rating_score,rating_count'])
            ->orderByDesc('created_at')
            ->get();

        return $this->success($offers, 'Offers retrieved.');
    }

    /**
     * GET /api/v1/offers/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $offer = Offer::with(['need', 'provider'])->find($id);
        if (!$offer) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        $user = $request->user();
        $isProvider = $offer->provider_id === $user->id;
        $isNeedOwner = $offer->need && $offer->need->requester_id === $user->id;

        if (!$isProvider && !$isNeedOwner) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        return $this->success($offer, 'Offer retrieved.');
    }

    /**
     * POST /api/v1/needs/{needId}/offers
     */
    public function store(StoreOfferRequest $request, string $needId): JsonResponse
    {
        $need = Need::find($needId);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        $user = $request->user();

        if ($need->requester_id === $user->id) {
            return $this->error('FORBIDDEN', 'Cannot offer on own Need.', 403);
        }

        if ($need->status !== Need::STATUS_OPEN) {
            return $this->error('STATE_CONFLICT', 'Need not open.', 409);
        }

        if ($need->offer_deadline_at && now()->gte($need->offer_deadline_at)) {
            return $this->error('OFFER_DEADLINE_PASSED', 'Offer deadline passed.', 422);
        }

        if ($need->offers()->where('provider_id', $user->id)->exists()) {
            return $this->error('OFFER_EXISTS', 'You already have an Offer.', 409);
        }

        $offer = DB::transaction(function () use ($request, $user, $need) {
            return Offer::create([
                'id' => (string) Str::uuid(),
                'need_id' => $need->id,
                'provider_id' => $user->id,
                'offered_price' => $request->input('offered_price'),
                'currency' => $request->input('currency', 'ETB'),
                'proposal_message' => $request->input('proposal_message'),
                'delivery_time_text' => $request->input('delivery_time_text'),
                'availability_text' => $request->input('availability_text'),
                'additional_notes' => $request->input('additional_notes'),
                'status' => Offer::STATUS_PENDING,
                'version' => 1,
            ]);
        });

        return $this->success($offer->fresh(), 'Offer submitted.', 201);
    }

    /**
     * PUT /api/v1/offers/{id}
     */
    public function update(UpdateOfferRequest $request, string $id): JsonResponse
    {
        $offer = Offer::find($id);
        if (!$offer) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        if ($offer->provider_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Not your Offer.', 403);
        }

        if ($offer->status !== Offer::STATUS_PENDING) {
            return $this->error('STATE_CONFLICT', 'Only PENDING Offers can be edited.', 409);
        }

        $need = $offer->need;
        if (!$need || $need->status !== Need::STATUS_OPEN) {
            return $this->error('STATE_CONFLICT', 'Need no longer OPEN.', 409);
        }

        $offer->fill($request->validated());
        $offer->version = $offer->version + 1;
        $offer->save();

        return $this->success($offer->fresh(), 'Offer updated.');
    }

    /**
     * POST /api/v1/offers/{id}/withdraw
     */
    public function withdraw(Request $request, string $id): JsonResponse
    {
        $offer = Offer::find($id);
        if (!$offer) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        if ($offer->provider_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Not your Offer.', 403);
        }

        if ($offer->status !== Offer::STATUS_PENDING) {
            return $this->error('STATE_CONFLICT', 'Only PENDING Offers can be withdrawn.', 409);
        }

        $offer->status = Offer::STATUS_WITHDRAWN;
        $offer->withdrawn_at = now();
        $offer->save();

        return $this->success($offer->fresh(), 'Offer withdrawn.');
    }

    /**
     * POST /api/v1/offers/{id}/accept
     */
    public function accept(Request $request, string $id): JsonResponse
    {
        $offer = Offer::find($id);
        if (!$offer) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        $need = $offer->need;
        if (!$need || $need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        if ($need->status !== Need::STATUS_OPEN) {
            return $this->error('STATE_CONFLICT', 'Need not OPEN.', 409);
        }

        if ($offer->status !== Offer::STATUS_PENDING) {
            return $this->error('STATE_CONFLICT', 'Offer not PENDING.', 409);
        }

        DB::transaction(function () use ($need, $offer, $request) {
            // Lock the Need
            $lockedNeed = Need::lockForUpdate()->find($need->id);

            if ($lockedNeed->award()->exists()) {
                throw new \RuntimeException('Already awarded');
            }

            // Create award
            DB::table('need_awards')->insert([
                'need_id' => $lockedNeed->id,
                'offer_id' => $offer->id,
                'accepted_by' => $request->user()->id,
                'accepted_at' => now(),
                'request_id' => $this->requestId(),
            ]);

            // Accept this offer
            $offer->status = Offer::STATUS_ACCEPTED;
            $offer->accepted_at = now();
            $offer->save();

            // Reject other pending offers
            Offer::where('need_id', $lockedNeed->id)
                ->where('id', '!=', $offer->id)
                ->where('status', Offer::STATUS_PENDING)
                ->update(['status' => Offer::STATUS_REJECTED]);

            // Update Need
            $lockedNeed->status = Need::STATUS_IN_PROGRESS;
            $lockedNeed->save();
        });

        return $this->success([
            'offer' => $offer->fresh(),
            'need' => $need->fresh(),
        ], 'Offer accepted. Need is now IN_PROGRESS.');
    }

    /**
     * POST /api/v1/offers/{id}/reject
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $offer = Offer::find($id);
        if (!$offer) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        $need = $offer->need;
        if (!$need || $need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        if ($offer->status !== Offer::STATUS_PENDING) {
            return $this->error('STATE_CONFLICT', 'Offer not PENDING.', 409);
        }

        $offer->status = Offer::STATUS_REJECTED;
        $offer->save();

        return $this->success($offer->fresh(), 'Offer rejected.');
    }

    /**
     * GET /api/v1/my/offers
     */
    public function myOffers(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 20), 50);
        $query = Offer::query()
            ->where('provider_id', $request->user()->id)
            ->with(['need:id,title,status,requester_id'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $offers = $query->paginate($perPage);

        return $this->success($offers->items(), 'My Offers retrieved.', 200, [
            'page' => $offers->currentPage(),
            'per_page' => $offers->perPage(),
            'total' => $offers->total(),
        ]);
    }
}
