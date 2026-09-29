<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    /**
     * GET /api/v1/notifications
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min((int) $request->input('per_page', 20), 50);

        $query = Notification::query()
            ->where('recipient_user_id', $user->id)
            ->where('channel', Notification::CHANNEL_IN_APP)
            ->orderByDesc('created_at');

        if ($request->boolean('unread')) {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate($perPage);

        return $this->success($notifications->items(), 'Notifications retrieved.', 200, [
            'page' => $notifications->currentPage(),
            'per_page' => $notifications->perPage(),
            'total' => $notifications->total(),
            'unread_count' => Notification::where('recipient_user_id', $user->id)
                ->where('channel', Notification::CHANNEL_IN_APP)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    /**
     * POST /api/v1/notifications/{id}/read
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return $this->error('NOT_FOUND', 'Notification not found.', 404);
        }

        if ($notification->recipient_user_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Not your notification.', 403);
        }

        $notification->markAsRead();

        return $this->success($notification->fresh(), 'Marked as read.');
    }
}
