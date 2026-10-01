<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Privacy\DataRightsService;
use App\Services\Privacy\DataExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Data Subject Rights API — Proclamation 1321/2024, Art. 34-39
 */
class PrivacyController extends Controller
{
    public function __construct(
        private DataRightsService $rights,
        private DataExportService $export
    ) {}

    // Art. 34 — Access
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->rights->show($request->user())]);
    }

    // Art. 35 — Rectification
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'sometimes|string|max:100',
            'email'    => 'sometimes|email|max:255',
            'username' => 'sometimes|string|max:50',
        ]);
        $updated = $this->rights->rectify($request->user(), $data);
        return response()->json(['data' => $updated]);
    }

    // Art. 36 — Erasure (request only, 7-day grace)
    public function destroy(Request $request): JsonResponse
    {
        $req = $this->rights->requestErasure($request->user(), $request);
        return response()->json(['data' => $req], 202);
    }

    // Art. 37 — Restriction
    public function restrict(Request $request): JsonResponse
    {
        $data = $request->validate([
            'scope'  => 'required|string|max:50',
            'reason' => 'required|string|max:500',
        ]);
        $req = $this->rights->restrict($request->user(), $data, $request);
        return response()->json(['data' => $req], 201);
    }

    // Art. 38 — Portability
    public function export(Request $request): JsonResponse
    {
        $data = $this->export->build($request->user());
        return response()->json(['data' => $data]);
    }

    // Art. 39 — Objection
    public function object(Request $request): JsonResponse
    {
        $data = $request->validate([
            'purpose' => 'required|string|max:50',
            'reason'  => 'required|string|max:500',
        ]);
        $req = $this->rights->object($request->user(), $data, $request);
        return response()->json(['data' => $req], 201);
    }
}
