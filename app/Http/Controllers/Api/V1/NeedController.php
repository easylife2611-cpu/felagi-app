<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Need\StoreNeedRequest;
use App\Http\Requests\Need\UpdateNeedRequest;
use App\Models\Need;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NeedController extends BaseApiController
{
    /**
     * GET /api/v1/needs
     * List public published OPEN needs.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'keyword'     => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'location'    => ['nullable', 'string', 'max:100'],
            'page'        => ['nullable', 'integer', 'min:1'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:50'],
            'sort'        => ['nullable', 'in:newest,budget_low,budget_high,deadline_soon'],
        ]);

        $query = Need::query()
            ->with(['category', 'requester:id,full_name,profile_photo_url,rating_score,rating_count'])
            ->where('status', Need::STATUS_OPEN)
            ->whereNull('archived_at')
            ->whereNull('deleted_at');

        if (!empty($validated['keyword'])) {
            $kw = $validated['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('description', 'like', "%{$kw}%");
            });
        }

        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        if (!empty($validated['location'])) {
            $query->where('location_text', 'like', '%' . $validated['location'] . '%');
        }

        $sort = $validated['sort'] ?? 'newest';
        match ($sort) {
            'budget_low'    => $query->orderBy('budget_min', 'asc'),
            'budget_high'   => $query->orderBy('budget_max', 'desc'),
            'deadline_soon' => $query->orderBy('deadline_at', 'asc')->orderByDesc('created_at'),
            default         => $query->orderByDesc('created_at'),
        };

        $perPage = (int) ($validated['per_page'] ?? 20);
        $needs = $query->paginate($perPage);

        return $this->success($needs->items(), 'Needs retrieved.', 200, [
            'page'     => $needs->currentPage(),
            'per_page' => $needs->perPage(),
            'total'    => $needs->total(),
            'has_more' => $needs->hasMorePages(),
        ]);
    }

    /**
     * GET /api/v1/needs/{id}
     * View single Need (public or owner projection).
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $need = Need::with(['category', 'requester:id,full_name,profile_photo_url,rating_score,rating_count'])
            ->find($id);

        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        $user = $request->user();
        $isOwner = $user && $need->requester_id === $user->id;

        if (!$isOwner && ($need->status === Need::STATUS_CANCELLED || $need->archived_at)) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        $data = $need->toArray();
        $data['offer_count'] = $need->offers()->where('status', 'PENDING')->count();
        $data['is_owner'] = $isOwner;

        return $this->success($data, 'Need retrieved.');
    }

    /**
     * POST /api/v1/needs
     * Create new Need.
     */
    public function store(StoreNeedRequest $request): JsonResponse
    {
        $user = $request->user();

        $need = DB::transaction(function () use ($request, $user) {
            return Need::create([
                'id' => (string) Str::uuid(),
                'requester_id' => $user->id,
                'category_id' => $request->input('category_id'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'location_text' => $request->input('location_text'),
                'budget_min' => $request->input('budget_min'),
                'budget_max' => $request->input('budget_max'),
                'currency' => $request->input('currency', 'ETB'),
                'quantity' => $request->input('quantity'),
                'deadline_at' => $request->input('deadline_at'),
                'offer_deadline_at' => $request->input('offer_deadline_at'),
                'status' => Need::STATUS_OPEN,
                'version' => 1,
                'telegram_publication_consent_at' => now(),
                'telegram_publication_version' => 1,
            ]);
        });

        return $this->success($need->fresh(['category']), 'Need created.', 201);
    }

    /**
     * PUT /api/v1/needs/{id}
     * Update owned Need (OPEN only).
     */
    public function update(UpdateNeedRequest $request, string $id): JsonResponse
    {
        $need = Need::find($id);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        $user = $request->user();
        if ($need->requester_id !== $user->id) {
            return $this->error('FORBIDDEN', 'Not your Need.', 403);
        }

        // T03: If-Match guard (DFM C13/C23)
        $ifMatch = $request->header('If-Match');
        if ($ifMatch !== null && (string) $ifMatch !== '' && (string) $ifMatch !== (string) $need->version) {
            return $this->error(
                'VERSION_CONFLICT',
                'Stale If-Match: resource version has changed. Refresh and retry.',
                409
            );
        }

        if ($need->status !== Need::STATUS_OPEN) {
            return $this->error('STATE_CONFLICT', 'Only OPEN Needs can be edited.', 409);
        }

        if ($need->award()->exists()) {
            return $this->error('STATE_CONFLICT', 'Need already awarded.', 409);
        }

        $need->fill($request->validated());
        $need->version = $need->version + 1;
        $need->save();

        return $this->success($need->fresh(), 'Need updated.');
    }

    /**
     * POST /api/v1/needs/{id}/cancel
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $need = Need::find($id);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        if ($need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Not your Need.', 403);
        }

        if ($need->status !== Need::STATUS_OPEN) {
            return $this->error('STATE_CONFLICT', 'Only OPEN Needs can be cancelled.', 409);
        }

        if ($need->award()->exists()) {
            return $this->error('STATE_CONFLICT', 'Cannot cancel awarded Need.', 409);
        }

        DB::transaction(function () use ($need) {
            $need->offers()->where('status', 'PENDING')->update(['status' => 'REJECTED']);
            $need->status = Need::STATUS_CANCELLED;
            $need->cancelled_at = now();
            $need->save();
        });

        return $this->success($need->fresh(), 'Need cancelled.');
    }

    /**
     * POST /api/v1/needs/{id}/complete
     */
    public function complete(Request $request, string $id): JsonResponse
    {
        $need = Need::find($id);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        if ($need->requester_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Not your Need.', 403);
        }

        if ($need->status !== Need::STATUS_IN_PROGRESS) {
            return $this->error('STATE_CONFLICT', 'Need must be IN_PROGRESS.', 409);
        }

        $need->status = Need::STATUS_COMPLETED;
        $need->completed_at = now();
        $need->save();

        return $this->success($need->fresh(), 'Need completed.');
    }

    /**
     * GET /api/v1/my/needs
     */
    public function myNeeds(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 20), 50);
        $query = Need::query()
            ->where('requester_id', $request->user()->id)
            ->with(['category'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $needs = $query->paginate($perPage);

        return $this->success($needs->items(), 'My Needs retrieved.', 200, [
            'page' => $needs->currentPage(),
            'per_page' => $needs->perPage(),
            'total' => $needs->total(),
        ]);
    }
}
