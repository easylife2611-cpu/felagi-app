<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Exceptions\SettingsVersionConflictException;
use App\Exceptions\ReauthRequiredException;
use App\Exceptions\TwoFactorRequiredException;
use App\Exceptions\IdempotencyConflictException;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Admin\CreateDraftRequest;
use App\Http\Requests\Admin\PublishChangeRequest;
use App\Models\Setting;
use App\Models\SettingDraft;
use App\Jobs\ProcessOutboxEvent;
use App\Jobs\VerifySettingChange;
use App\Models\SettingVersion;
use App\Services\Admin\AdminChangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminChangeController extends BaseApiController
{
    public function __construct(
        private readonly AdminChangeService $service,
    ) {}

    /**
     * POST /api/v1/admin/changes
     */
    public function store(CreateDraftRequest $request): JsonResponse
    {
        $this->authorize('create', Setting::class);

        $draft = $this->service->createDraft(
            $request->user(),
            $request->string('setting_key')->toString(),
            $request->input('proposed_value'),
        );

        return $this->success($draft, 'Draft created.', 201);
    }

    /**
     * GET /api/v1/admin/changes/{id}
     */
    public function show(string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('view', $setting);

        return $this->success($draft, 'Draft retrieved.');
    }

    /**
     * POST /api/v1/admin/changes/{id}/validate
     */
    public function validateDraft(string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('update', $setting);

        return $this->success(
            $this->service->validateDraft($draft),
            'Validation complete.',
        );
    }

    /**
     * POST /api/v1/admin/changes/{id}/simulate
     */
    public function simulate(string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('view', $setting);

        return $this->success(
            $this->service->simulate($draft),
            'Simulation complete.',
        );
    }

    /**
     * POST /api/v1/admin/changes/{id}/preview
     */
    public function preview(string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('update', $setting);

        return $this->success(
            $this->service->preview($draft),
            'Preview recorded.',
        );
    }

    /**
     * POST /api/v1/admin/changes/{id}/publish
     */
    public function publish(PublishChangeRequest $request, string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('publish', $setting);

        try {
            $version = $this->service->publish(
                $request->user(),
                $draft,
                $request->string('reason')->toString(),
                (int) $request->input('expected_version'),
                $request->input('confirmation_digest'),
            );
        } catch (SettingsVersionConflictException $e) {
            return $this->error(
                'SETTINGS_VERSION_CONFLICT',
                $e->getMessage(),
                409,
                $e->toArray(),
            );
        } catch (ReauthRequiredException $e) {
            return $this->error(
                'REAUTH_REQUIRED',
                $e->getMessage(),
                401,
                $e->toArray(),
            );
        } catch (TwoFactorRequiredException $e) {
            return $this->error(
                'TWO_FACTOR_REQUIRED',
                $e->getMessage(),
                403,
                $e->toArray(),
            );
        } catch (IdempotencyConflictException $e) {
            return $this->error(
                'IDEMPOTENCY_CONFLICT',
                $e->getMessage(),
                409,
                $e->toArray(),
            );
        }

        return $this->success($version, 'Published.', 200);
    }

    /**
     * GET /api/v1/admin/changes/{id}/audit
     */
    public function audit(string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('view', $setting);

        $rows = DB::table('audit_logs')
            ->where('entity_type', 'setting')
            ->where('safe_metadata->setting_key', $setting->key)
            ->orderByDesc('occurred_at')
            ->limit(100)
            ->get();

        return $this->success($rows, 'Audit trail.');
    }

    /**
     * POST /api/v1/admin/changes/{id}/apply
     *
     * Design: "Apply and verification are server jobs, never browser assertions."
     * Queues the outbox consumer for this draft's published version.
     */
    public function apply(Request $request, string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('publish', $setting);

        if ($draft->status !== SettingDraft::STATUS_PUBLISHED) {
            return $this->error(
                'STATE_CONFLICT',
                'Only PUBLISHED drafts can be applied.',
                409,
                ['current_status' => $draft->status],
            );
        }

        $event = DB::table('outbox_events')
            ->where('aggregate_type', 'setting_version')
            ->where('payload_json->setting_key', $setting->key)
            ->orderByDesc('created_at')
            ->first();

        if (!$event) {
            return $this->error(
                'NO_OUTBOX_EVENT',
                'No outbox event found for this setting. Publish first.',
                404,
            );
        }

        ProcessOutboxEvent::dispatch($event->id);

        return $this->success(
            ['outbox_event_id' => $event->id, 'status' => 'QUEUED'],
            'Apply queued.',
            202,
        );
    }

    /**
     * POST /api/v1/admin/changes/{id}/verify
     *
     * Design: "verification are server jobs, never browser assertions."
     * Queues a server-side probe for the latest published version.
     */
    public function verify(Request $request, string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('view', $setting);

        $latest = SettingVersion::where('setting_key', $setting->key)
            ->orderByDesc('version_number')
            ->first();

        if (!$latest) {
            return $this->error('NO_VERSION', 'No published version to verify.', 404);
        }

        VerifySettingChange::dispatch($setting->key, $latest->version_number);

        return $this->success(
            [
                'setting_key'    => $setting->key,
                'version_number' => $latest->version_number,
                'status'         => 'QUEUED',
            ],
            'Verification queued.',
            202,
        );
    }

    /**
     * POST /api/v1/admin/changes/{id}/rollback
     */
    public function rollback(Request $request, string $id): JsonResponse
    {
        $draft   = SettingDraft::findOrFail($id);
        $setting = Setting::findOrFail($draft->setting_key);
        $this->authorize('publish', $setting);

        $newDraft = $this->service->rollback(
            $request->user(),
            $setting->key,
            (int) $request->input('target_version'),
        );

        return $this->success($newDraft, 'Rollback draft created.', 201);
    }
}
