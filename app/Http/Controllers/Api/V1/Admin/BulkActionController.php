<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\BulkAction;
use App\Services\Admin\BulkActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AM (audit L276) — Bulk action endpoints.
 */
class BulkActionController extends BaseApiController
{
    public function __construct(
        private readonly BulkActionService $service,
    ) {}

    /** POST /api/v1/admin/bulk/preview */
    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'action_type'      => ['required', 'string', 'in:' . implode(',', BulkActionService::SUPPORTED_ACTIONS)],
            'entity_type'      => ['required', 'string', 'max:60'],
            'scope'            => ['required', 'string', 'max:120'],
            'selection_ids'    => ['required', 'array', 'min:1', 'max:' . BulkActionService::MAX_BATCH],
            'selection_ids.*'  => ['string', 'max:120'],
        ]);

        try {
            $preview = $this->service->preview(
                $request->user(),
                $data['action_type'],
                $data['entity_type'],
                $data['scope'],
                $data['selection_ids'],
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error('INVALID_SELECTION', $e->getMessage(), 422);
        }

        return $this->success($preview, 'Bulk preview.');
    }

    /** POST /api/v1/admin/bulk/execute */
    public function execute(Request $request): JsonResponse
    {
        $data = $request->validate([
            'action_type'      => ['required', 'string', 'in:' . implode(',', BulkActionService::SUPPORTED_ACTIONS)],
            'entity_type'      => ['required', 'string', 'max:60'],
            'scope'            => ['required', 'string', 'max:120'],
            'selection_ids'    => ['required', 'array', 'min:1', 'max:' . BulkActionService::MAX_BATCH],
            'selection_ids.*'  => ['string', 'max:120'],
            'selection_digest' => ['required', 'string', 'size:64'],
        ]);

        try {
            $bulk = $this->service->execute($request->user(), $data);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'SELECTION_DIGEST_MISMATCH') {
                return $this->error(
                    'SELECTION_DIGEST_MISMATCH',
                    'Selection changed since preview — re-preview and confirm.',
                    409,
                );
            }
            return $this->error('BULK_EXECUTE_FAILED', $e->getMessage(), 422);
        } catch (\InvalidArgumentException $e) {
            return $this->error('INVALID_SELECTION', $e->getMessage(), 422);
        }

        return $this->success($bulk, 'Bulk action executed.', 201);
    }

    /** POST /api/v1/admin/bulk/{id}/retry-failed */
    public function retryFailed(Request $request, string $id): JsonResponse
    {
        $original = BulkAction::find($id);
        if (! $original) {
            return $this->error('NOT_FOUND', 'Bulk action not found.', 404);
        }

        try {
            $retry = $this->service->retryFailed($request->user(), $original);
        } catch (\RuntimeException $e) {
            return $this->error('BULK_RETRY_FAILED', $e->getMessage(), 422);
        }

        return $this->success($retry, 'Bulk retry executed.', 201);
    }
}
