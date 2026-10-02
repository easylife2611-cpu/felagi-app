<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comparison;
use App\Models\Export;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExportController extends BaseApiController
{
    /**
     * POST /api/v1/comparisons/{id}/exports
     * DFM §257: 202 export id; 409 NOT_READY; 429 quota.
     */
    public function store(Request $request, string $id): JsonResponse
    {
        $comparison = Comparison::find($id);
        if (! $comparison) {
            return $this->error('NOT_FOUND', 'Comparison not found.', 404);
        }

        $need = $comparison->need;
        if (! $need || $need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        if ($comparison->status !== Comparison::STATUS_DONE) {
            return $this->error('NOT_READY', 'Comparison not completed.', 409);
        }

        $format = strtoupper((string) $request->input('format', 'PDF'));
        if (! in_array($format, ['PDF', 'XLSX', 'CSV', 'TXT'], true)) {
            return $this->error('INVALID_FORMAT', 'Unsupported format.', 422);
        }

        $export = Export::create([
            'comparison_id' => $comparison->id,
            'requester_id'  => $request->user()->id,
            'format'        => $format,
            'status'        => Export::STATUS_READY,
            'storage_key'   => 'exports/' . $comparison->id . '.' . strtolower($format),
            'expires_at'    => now()->addDays(7),
            'created_at'    => now(),
            'completed_at'  => now(),
        ]);

        return $this->success([
            'id'         => $export->id,
            'status'     => $export->status,
            'format'     => $export->format,
            'expires_at' => $export->expires_at?->toIso8601String(),
        ], 'Export queued.', 202);
    }

    /**
     * GET /api/v1/exports/{id}
     * DFM §258: 200 status, expires_at, download link when READY.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $export = Export::find($id);
        if (! $export) {
            return $this->error('NOT_FOUND', 'Export not found.', 404);
        }

        if ($export->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        $payload = [
            'id'           => $export->id,
            'status'       => $export->status,
            'format'       => $export->format,
            'expires_at'   => $export->expires_at?->toIso8601String(),
            'completed_at' => $export->completed_at?->toIso8601String(),
        ];

        if ($export->status === Export::STATUS_READY
            && $export->expires_at
            && $export->expires_at->isFuture()
        ) {
            $payload['download_url'] = '/api/v1/exports/' . $export->id . '/download';
        }

        return $this->success($payload, 'Export status.');
    }

    /**
     * GET /api/v1/exports/{id}/download
     * DFM §259: owner, READY, unexpired; stream with safe filename.
     * Binary streaming deferred to storage adapter.
     */
    public function download(Request $request, string $id): JsonResponse
    {
        $export = Export::find($id);
        if (! $export) {
            return $this->error('NOT_FOUND', 'Export not found.', 404);
        }

        if ($export->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Owner only.', 403);
        }

        if ($export->status !== Export::STATUS_READY) {
            return $this->error('NOT_READY', 'Export not ready.', 409);
        }

        if ($export->expires_at && $export->expires_at->isPast()) {
            return $this->error('EXPIRED', 'Export expired.', 410);
        }

        return $this->success([
            'id'          => $export->id,
            'format'      => $export->format,
            'storage_key' => $export->storage_key,
            'note'        => 'Binary streaming handled by storage adapter (design-deferred).',
        ], 'Export download.');
    }
}
