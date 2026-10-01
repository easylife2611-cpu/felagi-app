<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\AdCampaign;
use App\Models\AdCreative;
use App\Models\Advertiser;
use App\Services\Ads\AdEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Admin Ads API — LOCKED spec:
 *   System_Specification/Sponsored_Advertising_Contract.md §Main Admin A023
 *
 * Endpoints (from contract):
 *   GET    /api/v1/admin/ads
 *   POST   /api/v1/admin/ads/advertisers
 *   POST   /api/v1/admin/ads/campaigns
 *   GET    /api/v1/admin/ads/campaigns/{id}
 *   PATCH  /api/v1/admin/ads/campaigns/{id}
 *   POST   /api/v1/admin/ads/campaigns/{id}/validate
 *   POST   /api/v1/admin/ads/campaigns/{id}/preview
 *   POST   /api/v1/admin/ads/campaigns/{id}/publish
 *   POST   /api/v1/admin/ads/campaigns/{id}/pause
 *   POST   /api/v1/admin/ads/campaigns/{id}/resume
 *   POST   /api/v1/admin/ads/campaigns/{id}/cancel-schedule
 *   POST   /api/v1/admin/ads/campaigns/{id}/archive
 *   POST   /api/v1/admin/ads/campaigns/{id}/rollback
 *   GET    /api/v1/admin/ads/campaigns/{id}/reports
 *   GET    /api/v1/admin/ads/campaigns/{id}/audit
 *   POST   /api/v1/admin/ads/destinations/validate
 */
class AdminAdsController extends BaseApiController
{
    public function __construct(
        private readonly AdEventService $eventService,
    ) {}

    /** GET /api/v1/admin/ads — summary */
    public function index(): JsonResponse
    {
        return $this->success([
            'master_enabled' => (bool) \Cache::get('ads.master_enabled', false),
            'counts' => [
                'draft'     => AdCampaign::where('status', AdCampaign::STATUS_DRAFT)->count(),
                'scheduled' => AdCampaign::where('status', AdCampaign::STATUS_SCHEDULED)->count(),
                'active'    => AdCampaign::where('status', AdCampaign::STATUS_ACTIVE)->count(),
                'paused'    => AdCampaign::where('status', AdCampaign::STATUS_PAUSED)->count(),
                'ended'     => AdCampaign::where('status', AdCampaign::STATUS_ENDED)->count(),
            ],
            'placements' => [
                AdCampaign::PLACEMENT_BROWSE      => (bool) \Cache::get('ads.placement.' . AdCampaign::PLACEMENT_BROWSE . '.enabled', false),
                AdCampaign::PLACEMENT_SEARCH      => (bool) \Cache::get('ads.placement.' . AdCampaign::PLACEMENT_SEARCH . '.enabled', false),
                AdCampaign::PLACEMENT_NEED_DETAIL => (bool) \Cache::get('ads.placement.' . AdCampaign::PLACEMENT_NEED_DETAIL . '.enabled', false),
            ],
            'today' => [
                'impressions' => null,
                'clicks'      => null,
                'note'        => 'UNKNOWN until instrumentation',
            ],
        ], 'Ads admin overview.');
    }

    /** POST /api/v1/admin/ads/advertisers */
    public function storeAdvertiser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:100'],
            'display_name'      => ['required', 'string', 'max:120'],
            'contact_reference' => ['nullable', 'string', 'max:255'],
            'notes'             => ['nullable', 'string'],
        ]);

        $adv = Advertiser::create(array_merge($validated, [
            'status' => Advertiser::STATUS_ACTIVE,
        ]));

        return $this->success($adv, 'Advertiser created.', 201);
    }

    /** POST /api/v1/admin/ads/campaigns */
    public function storeCampaign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'advertiser_id'    => ['required', 'uuid', 'exists:advertisers,id'],
            'campaign_name'    => ['required', 'string', 'max:120'],
            'start_at'         => ['required', 'date'],
            'end_at'           => ['required', 'date', 'after:start_at'],
            'timezone'         => ['nullable', 'string', 'max:64'],
            'placement_ids'    => ['required', 'array', 'min:1'],
            'placement_ids.*'  => ['string', 'in:' . implode(',', AdCampaign::PLACEMENTS)],
            'destination_type' => ['required', 'in:INTERNAL,EXTERNAL'],
            'destination_value'=> ['required', 'string', 'max:500'],
            'priority'         => ['nullable', 'integer', 'min:0', 'max:100'],
            'commercial_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $campaign = AdCampaign::create(array_merge($validated, [
            'status'     => AdCampaign::STATUS_DRAFT,
            'version'    => 1,
            'created_by' => $request->user()->id,
        ]));

        return $this->success($campaign, 'Campaign draft created.', 201);
    }

    /** GET /api/v1/admin/ads/campaigns/{id} */
    public function showCampaign(string $id): JsonResponse
    {
        $c = AdCampaign::with(['advertiser', 'creative'])->find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }
        return $this->success($c, 'Campaign retrieved.');
    }

    /** PATCH /api/v1/admin/ads/campaigns/{id} */
    public function updateCampaign(Request $request, string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        $validated = $request->validate([
            'campaign_name'    => ['sometimes', 'string', 'max:120'],
            'start_at'         => ['sometimes', 'date'],
            'end_at'           => ['sometimes', 'date'],
            'placement_ids'    => ['sometimes', 'array'],
            'placement_ids.*'  => ['string', 'in:' . implode(',', AdCampaign::PLACEMENTS)],
            'priority'         => ['sometimes', 'integer', 'min:0', 'max:100'],
        ]);

        $c->fill($validated);
        // Any edit returns to DRAFT (contract rule)
        if ($c->status !== AdCampaign::STATUS_DRAFT) {
            $c->status = AdCampaign::STATUS_DRAFT;
        }
        $c->version += 1;
        $c->save();

        return $this->success($c->fresh(), 'Campaign updated.');
    }

    /** POST /api/v1/admin/ads/campaigns/{id}/validate */
    public function validateCampaign(string $id): JsonResponse
    {
        $c = AdCampaign::with('creative')->find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        $errors = [];

        if (! $c->creative) {
            $errors['creative'] = 'Creative is required.';
        }
        if (empty($c->placement_ids)) {
            $errors['placement_ids'] = 'At least one placement is required.';
        }
        if (! $c->start_at || ! $c->end_at) {
            $errors['schedule'] = 'Start and end are required.';
        }
        if ($c->end_at && $c->start_at && $c->end_at->lte($c->start_at)) {
            $errors['end_at'] = 'End must be after start.';
        }
        if ($c->destination_type === 'EXTERNAL') {
            if (! preg_match('/^https:\/\/[a-zA-Z0-9.-]+/', $c->destination_value)) {
                $errors['destination_value'] = 'External must be HTTPS URL.';
            }
        } elseif ($c->destination_type === 'INTERNAL') {
            $allowed = ['/browse'];
            if (! in_array($c->destination_value, $allowed, true)
                && ! preg_match('/^\/needs\/[a-f0-9-]+$/i', $c->destination_value)) {
                $errors['destination_value'] = 'Internal destination must be /browse or /needs/{id}.';
            }
        }

        if (! empty($errors)) {
            return $this->validationError($errors);
        }

        $c->status = AdCampaign::STATUS_VALIDATED;
        $c->save();

        return $this->success(['status' => $c->status], 'Campaign validated.');
    }

    /** POST /api/v1/admin/ads/campaigns/{id}/preview */
    public function previewCampaign(string $id): JsonResponse
    {
        $c = AdCampaign::with(['advertiser', 'creative'])->find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        return $this->success([
            'campaign'     => $c,
            'schedule_utc' => [
                'start' => $c->start_at?->toIso8601String(),
                'end'   => $c->end_at?->toIso8601String(),
            ],
            'timezone'     => $c->timezone,
            'placements'   => $c->placement_ids,
            'organic_unchanged' => true,
            'ai_unchanged'      => true,
        ], 'Campaign preview.');
    }

    /** POST /api/v1/admin/ads/campaigns/{id}/publish */
    public function publishCampaign(Request $request, string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        if ($c->status !== AdCampaign::STATUS_VALIDATED) {
            return $this->error('STATE_CONFLICT', 'Only VALIDATED campaigns can be published.', 409);
        }

        $c->status = $c->start_at->isFuture()
            ? AdCampaign::STATUS_SCHEDULED
            : AdCampaign::STATUS_ACTIVE;
        $c->published_at = now();
        $c->approved_by = $request->user()->id;
        $c->save();

        return $this->success($c->fresh(), 'Campaign published.');
    }

    /** POST /api/v1/admin/ads/campaigns/{id}/pause */
    public function pauseCampaign(string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        $c->status = AdCampaign::STATUS_PAUSED;
        $c->paused_at = now();
        $c->save();

        return $this->success($c->fresh(), 'Campaign paused.');
    }

    /** POST /api/v1/admin/ads/campaigns/{id}/resume */
    public function resumeCampaign(string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        $c->status = $c->start_at->isFuture()
            ? AdCampaign::STATUS_SCHEDULED
            : AdCampaign::STATUS_ACTIVE;
        $c->paused_at = null;
        $c->version += 1;
        $c->save();

        return $this->success($c->fresh(), 'Campaign resumed.');
    }

    /** POST /api/v1/admin/ads/campaigns/{id}/cancel-schedule */
    public function cancelSchedule(string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        $c->status = AdCampaign::STATUS_PAUSED;
        $c->paused_at = now();
        $c->save();

        return $this->success($c->fresh(), 'Schedule cancelled (paused).');
    }

    /** POST /api/v1/admin/ads/campaigns/{id}/archive */
    public function archiveCampaign(string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        $allowed = [AdCampaign::STATUS_ENDED, AdCampaign::STATUS_REJECTED, AdCampaign::STATUS_EXPIRED];
        if (! in_array($c->status, $allowed, true)) {
            return $this->error('STATE_CONFLICT', 'Only ENDED/REJECTED/EXPIRED can be archived.', 409);
        }

        $c->status = AdCampaign::STATUS_ARCHIVED;
        $c->save();

        return $this->success($c->fresh(), 'Campaign archived.');
    }

    /** POST /api/v1/admin/ads/campaigns/{id}/rollback */
    public function rollbackCampaign(Request $request, string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        // Rollback is additive: create a new version from prior values
        // (Simplified here — full implementation tracks versions in a separate table)
        $c->version += 1;
        $c->save();

        return $this->success($c->fresh(), 'Campaign version incremented (rollback base).');
    }

    /** GET /api/v1/admin/ads/campaigns/{id}/reports */
    public function reportCampaign(string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        $report = $this->eventService->campaignReport($id);

        return $this->success($report, 'Campaign report.');
    }

    /** GET /api/v1/admin/ads/campaigns/{id}/audit */
    public function auditCampaign(string $id): JsonResponse
    {
        $c = AdCampaign::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Campaign not found.', 404);
        }

        return $this->success([
            'campaign_id'    => $c->id,
            'current_version'=> $c->version,
            'created_by'     => $c->created_by,
            'approved_by'    => $c->approved_by,
            'published_at'   => $c->published_at?->toIso8601String(),
            'paused_at'      => $c->paused_at?->toIso8601String(),
            'ended_at'       => $c->ended_at?->toIso8601String(),
        ], 'Campaign audit.');
    }

    /** POST /api/v1/admin/ads/destinations/validate */
    public function validateDestination(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type'  => ['required', 'in:INTERNAL,EXTERNAL'],
            'value' => ['required', 'string', 'max:500'],
        ]);

        if ($validated['type'] === 'EXTERNAL') {
            if (! preg_match('/^https:\/\//', $validated['value'])) {
                return $this->error('INVALID_DESTINATION', 'External must use HTTPS.', 422);
            }
            if (preg_match('/@|localhost|127\.|10\.|192\.168\.|169\.254\./', $validated['value'])) {
                return $this->error('INVALID_DESTINATION', 'Private/loopback URLs blocked.', 422);
            }
        }

        return $this->success(['valid' => true], 'Destination validated.');
    }

    /** POST /api/v1/admin/ads/creatives/{id}/validate — ADS-17 */
    public function validateCreative(Request $request, string $id): JsonResponse
    {
        $c = AdCreative::find($id);
        if (! $c) {
            return $this->error('NOT_FOUND', 'Creative not found.', 404);
        }

        /** @var \App\Services\Ads\AdCreativeValidator $validator */
        $validator = app(\App\Services\Ads\AdCreativeValidator::class);
        $errors = $validator->validate($c);

        if (! empty($errors)) {
            $c->validation_receipt = [
                'status'       => 'INVALID',
                'errors'       => $errors,
                'validated_at' => now()->toIso8601String(),
                'validated_by' => $request->user()?->id,
                'spec'         => 'ADS-17',
            ];
            $c->save();

            return $this->validationError($errors);
        }

        $receipt = [
            'status'                => 'VALID',
            'errors'                => [],
            'validated_at'          => now()->toIso8601String(),
            'validated_by'          => $request->user()?->id,
            'spec'                  => 'ADS-17',
            'format'                => $c->format,
            'has_media'             => ! empty($c->media_asset_id),
            'human_review_required' => ! empty($c->media_asset_id),
            'human_review_note'     => 'Automated checks do not replace content review (ADS-17).',
        ];

        $c->validation_receipt = $receipt;
        $c->save();

        return $this->success($receipt, 'Creative validated.');
    }
}
