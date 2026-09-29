<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends BaseApiController
{
    /**
     * GET /api/v1/offers/{offerId}/messages
     * List messages for a conversation (participants only).
     */
    public function index(Request $request, string $offerId): JsonResponse
    {
        $offer = Offer::find($offerId);
        if (!$offer) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        $user = $request->user();
        $isParticipant = $offer->provider_id === $user->id
            || ($offer->need && $offer->need->requester_id === $user->id);

        if (!$isParticipant) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        $perPage = min((int) $request->input('per_page', 30), 100);
        $messages = $offer->messages()
            ->with('sender:id,full_name,profile_photo_url')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->success($messages->items(), 'Messages retrieved.', 200, [
            'page' => $messages->currentPage(),
            'per_page' => $messages->perPage(),
            'total' => $messages->total(),
        ]);
    }

    /**
     * POST /api/v1/offers/{offerId}/messages
     */
    public function store(Request $request, string $offerId): JsonResponse
    {
        $offer = Offer::with('need')->find($offerId);
        if (!$offer) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        $user = $request->user();
        $isParticipant = $offer->provider_id === $user->id
            || ($offer->need && $offer->need->requester_id === $user->id);

        if (!$isParticipant) {
            return $this->error('NOT_FOUND', 'Offer not found.', 404);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $message = Message::create([
            'id' => (string) Str::uuid(),
            'offer_id' => $offer->id,
            'sender_id' => $user->id,
            'content' => $validated['content'],
            'created_at' => now(),
        ]);

        return $this->success($message->fresh(['sender:id,full_name,profile_photo_url']), 'Message sent.', 201);
    }
}
