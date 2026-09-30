<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends BaseApiController
{
    /**
     * POST /api/v1/reports
     * Submit a report (authenticated users only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reason_code'  => ['required', 'string', 'in:SPAM,HARASSMENT,FRAUD,INAPPROPRIATE,OTHER'],
            'entity_type'  => ['required', 'string', 'in:NEED,OFFER,MESSAGE,USER'],
            'entity_id'    => ['required', 'uuid'],
            'details'      => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        $user = $request->user();

        $report = Report::create([
            'id'          => (string) Str::uuid(),
            'reporter_id' => $user->id,
            'entity_type' => $validated['entity_type'],
            'entity_id'   => $validated['entity_id'],
            'reason_code' => $validated['reason_code'],
            'details'     => $validated['details'],
            'status'      => Report::STATUS_OPEN,
        ]);

        return $this->success($report, 'Report submitted.', 201);
    }
}
