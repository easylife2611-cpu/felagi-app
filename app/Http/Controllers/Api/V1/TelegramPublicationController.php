<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Need;
use App\Models\TelegramPublication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramPublicationController extends BaseApiController
{
    /**
     * GET /api/v1/needs/{needId}/telegram-publications
     * List all publications for a need (owner only).
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

        $publications = TelegramPublication::query()
            ->where('need_id', $need->id)
            ->orderByDesc('created_at')
            ->get();

        return $this->success($publications, 'Publications retrieved.');
    }

    /**
     * POST /api/v1/needs/{needId}/telegram-publication/stop
     * Request stop of a specific publication (owner only).
     */
    public function stop(Request $request, string $needId): JsonResponse
    {
        $need = Need::find($needId);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }
        if ($need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        $validated = $request->validate([
            'publication_id' => ['nullable', 'uuid'],
        ]);

        $query = TelegramPublication::where('need_id', $need->id);
        if (!empty($validated['publication_id'])) {
            $query->where('id', $validated['publication_id']);
        }
        $publications = $query->get();

        if ($publications->isEmpty()) {
            return $this->error('NOT_FOUND', 'No publication found.', 404);
        }

        foreach ($publications as $pub) {
            if (in_array($pub->status, ['SENT', 'PENDING'])) {
                $pub->status = 'STOPPED';
                $pub->stopped_at = now();
                $pub->save();
            }
        }

        return $this->success($publications->fresh(), 'Publication(s) stopped.');
    }
}
