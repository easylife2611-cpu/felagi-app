<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Admin\AdminListRequest;
use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Setting;
use App\Models\TelegramDestination;
use App\Models\User;
use App\Policies\AdminReadPolicy;
use App\Services\Admin\ControlDependencyService;
use App\Services\Admin\ConfigDriftDetector;
use App\Services\Admin\AdminSecurityService;
use App\Services\Admin\AdminRecoveryService;
use App\Services\Admin\AdminSafeModeService;
use App\Services\Admin\AdminSettingsStatusService;
use App\Services\Admin\AdminMaintenanceService;
use App\Services\Admin\AdminFeaturesService;
use App\Services\Admin\AdminMonetizationService;
use App\Services\Admin\AdminMarketplaceService;
use App\Services\Admin\AdminJobsService;
use App\Services\Admin\AdminBackupsService;
use App\Services\Admin\AdminIntegrityService;
use App\Services\Admin\AdminMetricsService;
use App\Services\Admin\AdminHealthService;
use Illuminate\Http\JsonResponse;

/**
 * WP-05c — Admin read endpoints (23 screens).
 *
 * Per docs/specs/WP-05c_LOCKED.md:
 *   - Capability-based auth via AdminReadPolicy
 *   - Offset pagination (default 25, max 100)
 *   - BaseApiController envelope
 *   - Audit log on every successful read
 *
 * Screens without a backing model return explicit placeholder data
 * (meta.source = 'placeholder') — never guessed values.
 */
class AdminReadController extends BaseApiController
{
    /** Map: screen → [area, model|null] */
    private const SCREENS = [
        'A001' => ['dashboard',     null],
        'A002' => ['telegram',      TelegramDestination::class],
        'A003' => ['health',        null],
        'A004' => ['features',      Setting::class],
        'A005' => ['marketplace',   Report::class],
        'A006' => ['ai',            null],
        'A007' => ['payments',      Payment::class],
        'A008' => ['users',         User::class],
        'A009' => ['content',       Setting::class],
        'A010' => ['notifications', null],
        'A011' => ['files',         Attachment::class],
        'A012' => ['jobs',          null],
        'A013' => ['backups',       null],
        'A014' => ['integrity',     null],
        'A015' => ['security',      null],
        'A016' => ['audit',         AuditLog::class],
        'A017' => ['settings',      Setting::class],
        'A018' => ['recovery',      null],
        'A019' => ['safe-mode',     null],
        'A020' => ['monetization',  Setting::class],
        'A021' => ['maintenance',   null],
        'A022' => ['reports',       Report::class],
        // A023 (Sponsored Ads) — handled by AdminAdsController (L267)
    ];

    // ─────────────────────────────────────────────────────────
    // A001–A023 — each method delegates to handle($screen, $req)
    // ─────────────────────────────────────────────────────────

    public function dashboard(AdminListRequest $r): JsonResponse      { return $this->handle('A001', $r); }
    public function telegram(AdminListRequest $r): JsonResponse       { return $this->handle('A002', $r); }
    public function health(AdminListRequest $r): JsonResponse         { return $this->handle('A003', $r); }

    // L305 — additive: real metrics (separate endpoint, does not alter A001 handle())
    public function dashboardMetrics(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'dashboard');
        if (! $allowed) {
            $this->auditDenied('A001', 'dashboard');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }

        $metrics = app(AdminMetricsService::class)->summary();

        return $this->success(
            data: ['stats' => $metrics, 'source' => 'live'],
            message: 'Dashboard metrics.',
            status: 200,
            meta: [
                'screen'    => 'A001',
                'area'      => 'dashboard',
                'source'    => 'live',
                'total'     => 0,
                'page'      => 1,
                'per_page'  => 20,
                'last_page' => 1,
            ]
        );
    }

    // L305 — additive: real health (separate endpoint, does not alter A003 handle())
    public function healthStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'health');
        if (! $allowed) {
            $this->auditDenied('A003', 'health');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }

        $status = app(AdminHealthService::class)->status();

        return $this->success(
            data: ['components' => $status, 'source' => 'live'],
            message: 'Health status.',
            status: 200,
            meta: [
                'screen'    => 'A003',
                'area'      => 'health',
                'source'    => 'live',
                'total'     => 0,
                'page'      => 1,
                'per_page'  => 20,
                'last_page' => 1,
            ]
        );
    }
    public function features(AdminListRequest $r): JsonResponse       { return $this->handle('A004', $r); }
    public function marketplace(AdminListRequest $r): JsonResponse    { return $this->handle('A005', $r); }
    public function ai(AdminListRequest $r): JsonResponse             { return $this->handle('A006', $r); }
    public function payments(AdminListRequest $r): JsonResponse       { return $this->handle('A007', $r); }
    public function users(AdminListRequest $r): JsonResponse          { return $this->handle('A008', $r); }
    public function content(AdminListRequest $r): JsonResponse        { return $this->handle('A009', $r); }
    public function notifications(AdminListRequest $r): JsonResponse  { return $this->handle('A010', $r); }
    public function files(AdminListRequest $r): JsonResponse          { return $this->handle('A011', $r); }
    public function jobs(AdminListRequest $r): JsonResponse           { return $this->handle('A012', $r); }
    public function backups(AdminListRequest $r): JsonResponse        { return $this->handle('A013', $r); }
    public function integrity(AdminListRequest $r): JsonResponse      { return $this->handle('A014', $r); }
    public function security(AdminListRequest $r): JsonResponse       { return $this->handle('A015', $r); }
    public function audit(AdminListRequest $r): JsonResponse          { return $this->handle('A016', $r); }
    public function settings(AdminListRequest $r): JsonResponse       { return $this->handle('A017', $r); }
    public function recovery(AdminListRequest $r): JsonResponse       { return $this->handle('A018', $r); }
    public function safeMode(AdminListRequest $r): JsonResponse       { return $this->handle('A019', $r); }
    public function monetization(AdminListRequest $r): JsonResponse   { return $this->handle('A020', $r); }
    public function maintenance(AdminListRequest $r): JsonResponse    { return $this->handle('A021', $r); }
    public function reports(AdminListRequest $r): JsonResponse        { return $this->handle('A022', $r); }

    // ─────────────────────────────────────────────────────────
    // Shared handler
    // ─────────────────────────────────────────────────────────

    private function handle(string $screen, AdminListRequest $req): JsonResponse
    {
        [$area, $modelClass] = self::SCREENS[$screen];

        // 1. Authorize (deny by default)
        $allowed = (new AdminReadPolicy())->viewAny($req->user(), $area);
        if (! $allowed) {
            // Failed denied attempt = security event (per contract)
            $this->auditDenied($screen, $area);
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }

        $page    = $req->pageNumber();
        $perPage = $req->perPage();

        // 2. Data source
        if ($modelClass === null) {
            // Placeholder: no backing model yet (infrastructure screen)
            $items = [];
            $total = 0;
            $source = 'placeholder';
        } else {
            $query = $modelClass::query();

            // Apply search if model has a `search` scope or common columns
            if (($q = $req->searchTerm()) !== null) {
                $query->where(function ($w) use ($q) {
                    // Conservative: no field guessing — only generic id match if nothing else known
                    $w->where('id', 'like', "%{$q}%");
                });
            }

            // Status filter (only if column exists on the model — no guessing)
            if (($s = $req->statusFilter()) !== null && $this->hasColumn($modelClass, 'status')) {
                $query->where('status', $s);
            }

            // Date filter (created_at if it exists)
            if (($from = $req->dateFrom()) !== null && $this->hasColumn($modelClass, 'created_at')) {
                $query->where('created_at', '>=', $from . ' 00:00:00');
            }
            if (($to = $req->dateTo()) !== null && $this->hasColumn($modelClass, 'created_at')) {
                $query->where('created_at', '<=', $to . ' 23:59:59');
            }

            $total  = $query->count();          // BEFORE pagination (per contract)

            // Order: use created_at only if it exists; else id
            if ($this->hasColumn($modelClass, 'created_at')) {
                $query->orderBy('created_at', 'desc');
            } elseif ($this->hasColumn($modelClass, 'id')) {
                $query->orderBy('id', 'desc');
            }

            $items = $query
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
                ->toArray();

            $source = 'model:' . class_basename($modelClass);
        }

        // 3. Audit successful read
        $this->auditRead($screen, $area, $total);

        // 4. Envelope
        return $this->success(
            data: $items,
            message: 'OK',
            status: 200,
            meta: [
                'screen'    => $screen,
                'area'      => $area,
                'source'    => $source,
                'total'     => $total,
                'page'      => $page,
                'per_page'  => $perPage,
                'last_page' => $perPage > 0 ? (int) ceil($total / $perPage) : 1,
            ]
        );
    }

    // ─────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────

    private function hasColumn(string $modelClass, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn((new $modelClass)->getTable(), $column);
        } catch (\Throwable) {
            return false;
        }
    }

    private function auditRead(string $screen, string $area, int $total): void
    {
        try {
            AuditLog::create([
                'actor_id'      => optional(request()->user())->id,
                'action'        => "admin.read.{$area}",
                'entity_type'   => 'admin_screen',
                'entity_id'     => $screen,
                'request_id'    => $this->requestId(),
                'reason'        => 'admin read',
                'before_digest' => null,
                'after_digest'  => null,
                'safe_metadata' => ['screen' => $screen, 'area' => $area, 'total' => $total],
                'occurred_at'   => now(),
                'prev_hash'     => null,
                'hash'          => hash('sha256', $screen . $area . $total . now()->toIso8601String()),
            ]);
        } catch (\Throwable) {
            // Audit must never break a successful read
        }
    }

    private function auditDenied(string $screen, string $area): void
    {
        try {
            AuditLog::create([
                'actor_id'      => optional(request()->user())->id,
                'action'        => "admin.read.denied.{$area}",
                'entity_type'   => 'admin_screen',
                'entity_id'     => $screen,
                'request_id'    => $this->requestId(),
                'reason'        => 'denied',
                'before_digest' => null,
                'after_digest'  => null,
                'safe_metadata' => ['screen' => $screen, 'area' => $area],
                'occurred_at'   => now(),
                'prev_hash'     => null,
                'hash'          => hash('sha256', 'denied' . $screen . $area . now()->toIso8601String()),
            ]);
        } catch (\Throwable) {
        }
    }

    /**
     * GET /api/v1/admin/controls/{key}/dependencies
     *
     * J (audit L276) — reports the dependency tree of a single setting
     * and whether each dependency is currently satisfied.
     */
    public function controlDependencies(string $key): JsonResponse
    {
        $setting = Setting::find($key);
        if (! $setting) {
            return $this->error('NOT_FOUND', 'Setting not found.', 404);
        }

        $allowed = (new AdminReadPolicy())->viewAny(request()->user(), 'features');
        if (! $allowed) {
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }

        $report = app(ControlDependencyService::class)->inspect($setting);

        return $this->success($report, 'Control dependencies.');
    }

    /**
     * GET /api/v1/admin/integrity/drift
     *
     * AC (audit L276) — configuration drift report.
     */
    // ─────────────────────────────────────────────────────────
    // L306 — Real data for A012/A013/A014 (additive)
    // ─────────────────────────────────────────────────────────

    public function jobsStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'jobs');
        if (! $allowed) {
            $this->auditDenied('A012', 'jobs');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }

        $status = app(AdminJobsService::class)->status();

        return $this->success(
            data: $status,
            message: 'Jobs status.',
            status: 200,
            meta: [
                'screen' => 'A012', 'area' => 'jobs', 'source' => 'live',
                'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1,
            ]
        );
    }

    public function backupsStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'backups');
        if (! $allowed) {
            $this->auditDenied('A013', 'backups');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }

        $status = app(AdminBackupsService::class)->status();

        return $this->success(
            data: $status,
            message: 'Backups status.',
            status: 200,
            meta: [
                'screen' => 'A013', 'area' => 'backups', 'source' => 'live',
                'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1,
            ]
        );
    }

    public function integrityStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'integrity');
        if (! $allowed) {
            $this->auditDenied('A014', 'integrity');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }

        $status = app(AdminIntegrityService::class)->status();

        return $this->success(
            data: $status,
            message: 'Integrity status.',
            status: 200,
            meta: [
                'screen' => 'A014', 'area' => 'integrity', 'source' => 'live',
                'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1,
            ]
        );
    }
    // ─────────────────────────────────────────────────────────
    // L307 — Real data for A015/A018/A019 (additive)
    // ─────────────────────────────────────────────────────────

    public function securityStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'security');
        if (! $allowed) {
            $this->auditDenied('A015', 'security');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }
        $status = app(AdminSecurityService::class)->status();
        return $this->success(
            data: $status,
            message: 'Security status.',
            status: 200,
            meta: ['screen' => 'A015', 'area' => 'security', 'source' => 'live',
                   'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1]
        );
    }

    public function recoveryStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'recovery');
        if (! $allowed) {
            $this->auditDenied('A018', 'recovery');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }
        $status = app(AdminRecoveryService::class)->status();
        return $this->success(
            data: $status,
            message: 'Recovery status.',
            status: 200,
            meta: ['screen' => 'A018', 'area' => 'recovery', 'source' => 'live',
                   'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1]
        );
    }

    public function safeModeStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'safe-mode');
        if (! $allowed) {
            $this->auditDenied('A019', 'safe-mode');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }
        $status = app(AdminSafeModeService::class)->status();
        return $this->success(
            data: $status,
            message: 'Safe mode status.',
            status: 200,
            meta: ['screen' => 'A019', 'area' => 'safe-mode', 'source' => 'live',
                   'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1]
        );
    }

    // ─────────────────────────────────────────────────────────
    // L308 — Real data for A017 (additive)
    // ─────────────────────────────────────────────────────────

    public function settingsStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'settings');
        if (! $allowed) {
            $this->auditDenied('A017', 'settings');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }
        $status = app(AdminSettingsStatusService::class)->status();
        return $this->success(
            data: $status,
            message: 'Settings status.',
            status: 200,
            meta: ['screen' => 'A017', 'area' => 'settings', 'source' => 'live',
                   'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1]
        );
    }


    // ─────────────────────────────────────────────────────────
    // L309 — Real data for A021 (additive)
    // ─────────────────────────────────────────────────────────

    public function maintenanceStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'maintenance');
        if (! $allowed) {
            $this->auditDenied('A021', 'maintenance');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }
        $status = app(AdminMaintenanceService::class)->status();
        return $this->success(
            data: $status,
            message: 'Maintenance status.',
            status: 200,
            meta: ['screen' => 'A021', 'area' => 'maintenance', 'source' => 'live',
                   'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1]
        );
    }


    // ─────────────────────────────────────────────────────────
    // L310 — Real data for A004 (additive)
    // ─────────────────────────────────────────────────────────

    public function featuresStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'features');
        if (! $allowed) {
            $this->auditDenied('A004', 'features');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }
        $status = app(AdminFeaturesService::class)->status();
        return $this->success(
            data: $status,
            message: 'Features status.',
            status: 200,
            meta: ['screen' => 'A004', 'area' => 'features', 'source' => 'live',
                   'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1]
        );
    }


    // ─────────────────────────────────────────────────────────
    // L311 — Real data for A020 (additive)
    // ─────────────────────────────────────────────────────────

    public function monetizationStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'monetization');
        if (! $allowed) {
            $this->auditDenied('A020', 'monetization');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }
        $status = app(AdminMonetizationService::class)->status();
        return $this->success(
            data: $status,
            message: 'Monetization status.',
            status: 200,
            meta: ['screen' => 'A020', 'area' => 'monetization', 'source' => 'live',
                   'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1]
        );
    }


    // ─────────────────────────────────────────────────────────
    // L312 — Real data for A005 (additive)
    // ─────────────────────────────────────────────────────────

    public function marketplaceStatus(AdminListRequest $r): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny($r->user(), 'marketplace');
        if (! $allowed) {
            $this->auditDenied('A005', 'marketplace');
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }
        $status = app(AdminMarketplaceService::class)->status();
        return $this->success(
            data: $status,
            message: 'Marketplace status.',
            status: 200,
            meta: ['screen' => 'A005', 'area' => 'marketplace', 'source' => 'live',
                   'total' => 0, 'page' => 1, 'per_page' => 20, 'last_page' => 1]
        );
    }

    public function configDrift(): JsonResponse
    {
        $allowed = (new AdminReadPolicy())->viewAny(request()->user(), 'integrity');
        if (! $allowed) {
            return $this->error('FORBIDDEN', 'Insufficient capability.', 403);
        }

        $report = app(ConfigDriftDetector::class)->detect();

        return $this->success($report, 'Config drift report.');
    }
}
