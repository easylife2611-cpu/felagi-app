<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BreachIncident;
use App\Services\Privacy\BreachNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Breach Notification — Proclamation 1321/2024, Art. 30
 * Admin-only endpoints
 */
class BreachController extends Controller
{
    public function __construct(private BreachNotificationService $service) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data'    => BreachIncident::orderByDesc('detected_at')->get(),
            'total'   => BreachIncident::count(),
            'overdue' => $this->service->overdueIncidents()->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'             => 'required|string|max:200',
            'description'       => 'required|string|max:5000',
            'severity'          => 'required|in:P1,P2,P3,P4',
            'data_categories'   => 'sometimes|array',
            'data_categories.*' => 'string|max:50',
            'affected_count'    => 'sometimes|integer|min:0',
        ]);

        $incident = $this->service->report($data, $request->user()?->id);

        return response()->json(['data' => $incident], 201);
    }

    public function show(BreachIncident $incident): JsonResponse
    {
        return response()->json([
            'data' => $incident,
            'meta' => [
                'hours_until_deadline' => $incident->hoursUntilDeadline(),
                'deadline_exceeded'    => $incident->isDeadlineExceeded(),
            ],
        ]);
    }

    public function contain(BreachIncident $incident): JsonResponse
    {
        return response()->json(['data' => $this->service->markContained($incident)]);
    }

    public function notifyEca(BreachIncident $incident): JsonResponse
    {
        return response()->json(['data' => $this->service->notifyEca($incident)]);
    }

    public function notifyUsers(Request $request, BreachIncident $incident): JsonResponse
    {
        $data = $request->validate([
            'user_ids'   => 'sometimes|array',
            'user_ids.*' => 'string|max:36',
        ]);

        $count = $this->service->notifyAffectedUsers($incident, $data['user_ids'] ?? []);

        return response()->json([
            'data' => $incident->fresh(),
            'sent' => $count,
        ]);
    }

    public function resolve(Request $request, BreachIncident $incident): JsonResponse
    {
        $data = $request->validate([
            'remediation' => 'required|string|max:5000',
        ]);

        return response()->json([
            'data' => $this->service->resolve($incident, $data['remediation']),
        ]);
    }

    public function overdue(): JsonResponse
    {
        return response()->json(['data' => $this->service->overdueIncidents()]);
    }
}
