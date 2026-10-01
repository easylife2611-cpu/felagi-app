<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ConsentLog;
use App\Services\Consent\ConsentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsentController extends Controller
{
    public function __construct(private ConsentService $consent) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->consent->all($request->user())]);
    }

    public function grant(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'   => 'required|string|in:' . implode(',', [
                ConsentLog::TYPE_MARKETING,
                ConsentLog::TYPE_ADS,
                ConsentLog::TYPE_AI_COMPARE,
                ConsentLog::TYPE_TELEGRAM,
                ConsentLog::TYPE_CROSS_BORDER,
            ]),
            'source' => 'required|string|max:50',
        ]);

        $log = $this->consent->grant(
            $request->user(),
            $data['type'],
            $data['source'],
            $request
        );

        return response()->json(['data' => $log], 201);
    }

    public function revoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'   => 'required|string|max:50',
            'source' => 'required|string|max:50',
        ]);

        $log = $this->consent->revoke(
            $request->user(),
            $data['type'],
            $data['source'],
            $request
        );

        if (! $log) {
            return response()->json(['message' => 'No active consent found'], 404);
        }

        return response()->json(['data' => $log]);
    }
}
