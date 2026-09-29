<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\TelegramDestination;
use App\Models\TelegramPublication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AdminTelegramController — read-only endpoints.
 *
 * Per Design_Integration_Contract.md:
 *   GET /admin/telegram/destinations
 *   GET /admin/telegram/publications
 *
 * Write endpoints (POST validate/activate/pause/retry) require
 * Telegram bot token → WP-27.
 */
class AdminTelegramController extends BaseApiController
{
    /**
     * GET /api/v1/admin/telegram/destinations
     * List destinations (paginated).
     */
    public function destinations(Request $request): JsonResponse
    {
        $query = TelegramDestination::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $perPage = min((int) $request->input('per_page', 20), 50);

        $paginator = $query
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->success($paginator->items(), 'Destinations retrieved.', 200, [
            'page'     => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total'    => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
        ]);
    }

    /**
     * GET /api/v1/admin/telegram/destinations/{id}
     * Single destination detail.
     */
    public function showDestination(string $id): JsonResponse
    {
        $dest = TelegramDestination::find($id);

        if (!$dest) {
            return $this->error('NOT_FOUND', 'Destination not found.', 404);
        }

        return $this->success([
            'destination'           => $dest,
            'publication_count'     => $dest->publications()->count(),
            'active_publications'   => $dest->publications()
                ->where('state', TelegramPublication::STATE_POSTED)
                ->count(),
            'can_publish'           => $dest->canPublish(),
        ], 'Destination retrieved.');
    }

    /**
     * GET /api/v1/admin/telegram/publications
     * List publications (paginated, filtered by state/destination).
     */
    public function publications(Request $request): JsonResponse
    {
        $query = TelegramPublication::query()->with(['need:id,title,status', 'destination:id,name,type,status']);

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->input('destination_id'));
        }

        if ($request->filled('need_id')) {
            $query->where('need_id', $request->input('need_id'));
        }

        $perPage = min((int) $request->input('per_page', 20), 50);

        $paginator = $query
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->success($paginator->items(), 'Publications retrieved.', 200, [
            'page'     => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total'    => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
        ]);
    }

    /**
     * GET /api/v1/admin/telegram/publications/{id}
     * Single publication detail with recent events.
     */
    public function showPublication(string $id): JsonResponse
    {
        $pub = TelegramPublication::with([
            'need:id,title,status',
            'destination:id,name,type,status',
            'events',
        ])->find($id);

        if (!$pub) {
            return $this->error('NOT_FOUND', 'Publication not found.', 404);
        }

        return $this->success([
            'publication' => $pub,
            'event_count' => $pub->events()->count(),
        ], 'Publication retrieved.');
    }
}
