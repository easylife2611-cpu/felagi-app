<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\SettingPreset;
use App\Services\Admin\PresetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AH (audit L276) — Safe presets / operation modes.
 */
class PresetController extends BaseApiController
{
    public function __construct(
        private readonly PresetService $service,
    ) {}

    /** GET /api/v1/admin/presets */
    public function index(): JsonResponse
    {
        return $this->success($this->service->list(), 'Presets.');
    }

    /** GET /api/v1/admin/presets/{name}/preview */
    public function preview(string $name): JsonResponse
    {
        $preset = SettingPreset::where('name', $name)->first();
        if (! $preset) {
            return $this->error('NOT_FOUND', 'Preset not found.', 404);
        }

        return $this->success($this->service->preview($preset), 'Preset preview.');
    }

    /** POST /api/v1/admin/presets/{name}/apply */
    public function apply(Request $request, string $name): JsonResponse
    {
        $preset = SettingPreset::where('name', $name)->first();
        if (! $preset) {
            return $this->error('NOT_FOUND', 'Preset not found.', 404);
        }

        try {
            $bulk = $this->service->apply($request->user(), $preset);
        } catch (\InvalidArgumentException $e) {
            return $this->error('PRESET_INVALID', $e->getMessage(), 422);
        }

        return $this->success($bulk, 'Preset applied.', 201);
    }
}
