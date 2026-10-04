<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdsDeliveryController;
use App\Http\Controllers\Api\V1\AdsEventController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\ComparisonController;
use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\NeedController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OfferController;
use App\Http\Controllers\Api\V1\RatingController;
use App\Http\Controllers\V1\TwoFactorController;
use App\Http\Controllers\Api\V1\Admin\AdminChangeController;
use App\Http\Controllers\Api\V1\Admin\AdminTelegramController;
use App\Http\Controllers\Api\V1\Admin\AdminReadController;
use App\Http\Controllers\Api\V1\Admin\BulkActionController;
use App\Http\Controllers\Api\V1\Admin\PresetController;
use App\Http\Controllers\Api\V1\Admin\AdminAdsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
*/

// Public health check (no auth)
Route::get("/health", HealthController::class);

// Sponsored Ads — public endpoints (no auth)
Route::prefix("ads")->group(function () {
    Route::get("placements/{id}/delivery", [AdsDeliveryController::class, "show"])
        ->middleware("throttle:120,1");
    Route::post("events", [AdsEventController::class, "store"])
        ->middleware("throttle:300,1");
});

Route::prefix('v1')->group(function () {

    // =========================================
    // AUTH
    // =========================================
    Route::prefix('auth')->group(function () {
        Route::post('telegram/start', [AuthController::class, 'telegramStart'])
            ->middleware('throttle:5,15');

        // Widget flow (BotFather "Web Login" unavailable)
        Route::post('telegram/widget/start', [AuthController::class, 'telegramWidgetStart'])
            ->middleware('throttle:60,1');

        Route::get('telegram/widget/callback', [AuthController::class, 'telegramWidgetCallback'])
            ->middleware('throttle:60,1');

        // WP-27: Telegram redirects user here with ?code=&state=
        Route::get('telegram/callback', [AuthController::class, 'telegramCallback'])
            ->middleware('throttle:10,1');

        // WP-27: Flutter exchanges single-use handoff code for app tokens
        Route::post('telegram/exchange', [AuthController::class, 'telegramExchange'])
            ->middleware('throttle:10,1');
        Route::post('refresh', [AuthController::class, 'refresh']);

        // WP-13c: 2FA endpoints (backend only — UI deferred per D-097)
        Route::prefix('2fa')->middleware('auth:sanctum')->group(function () {
            Route::get('status', [TwoFactorController::class, 'status']);
            Route::post('enroll/start', [TwoFactorController::class, 'enrollStart'])->middleware('throttle:10,1');
            Route::post('enroll/verify', [TwoFactorController::class, 'enrollVerify'])->middleware('throttle:10,1');
            Route::post('verify', [TwoFactorController::class, 'verify'])->middleware('throttle:10,1');
            Route::post('recovery', [TwoFactorController::class, 'recovery'])->middleware('throttle:10,1');
            Route::post('recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])->middleware('throttle:5,1');
            Route::post('disable', [TwoFactorController::class, 'disable'])->middleware('throttle:5,1');
        });

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // =========================================
    // PUBLIC
    // =========================================
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('needs', [NeedController::class, 'index'])
        ->middleware('throttle:120,1');
    Route::get('needs/{id}', [NeedController::class, 'show']);

    // =========================================
    // AUTHENTICATED
    // =========================================
    Route::middleware('auth:sanctum')->group(function () {

        // Profile
        Route::patch('profile', [AuthController::class, 'updateProfile']);
        Route::post('profile/photo', [AuthController::class, 'uploadProfilePhoto']);
        Route::post('attachments', [AttachmentController::class, 'store']);

        // Needs
        Route::post('needs', [NeedController::class, 'store'])
            ->middleware('throttle:60,1');
        Route::put('needs/{id}', [NeedController::class, 'update']);
        Route::post('needs/{id}/cancel', [NeedController::class, 'cancel']);
        Route::post('needs/{id}/complete', [NeedController::class, 'complete']);

        // My resources
        Route::get('my/needs', [NeedController::class, 'myNeeds']);
        Route::get('my/offers', [OfferController::class, 'myOffers']);

        // Offers
        Route::get('needs/{needId}/offers', [OfferController::class, 'index']);
        Route::post('needs/{needId}/offers', [OfferController::class, 'store'])
            ->middleware('throttle:60,1');
        Route::get('offers/{id}', [OfferController::class, 'show']);
        Route::put('offers/{id}', [OfferController::class, 'update']);
        Route::post('offers/{id}/withdraw', [OfferController::class, 'withdraw']);
        Route::post('offers/{id}/reject', [OfferController::class, 'reject']);
        Route::post('offers/{id}/accept', [OfferController::class, 'accept']);

        // Messages
        Route::get('offers/{offerId}/messages', [MessageController::class, 'index']);
        Route::post('offers/{offerId}/messages', [MessageController::class, 'store'])
            ->middleware('throttle:30,1');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markRead']);

        // Ratings
        Route::post('needs/{needId}/ratings', [RatingController::class, 'store']);

        // Comparisons
        Route::get('needs/{needId}/comparisons', [ComparisonController::class, 'index']);
        Route::post('needs/{needId}/comparisons', [ComparisonController::class, 'store']);
        Route::get('comparisons/{id}', [ComparisonController::class, 'show']);
        Route::get('comparisons/{id}/results', [ComparisonController::class, 'results']);
        Route::post('comparisons/{id}/retry', [ComparisonController::class, 'retry']);
        Route::post('comparisons/{id}/exports', [ExportController::class, 'store']);
        Route::get('my/comparisons', [ComparisonController::class, 'myComparisons']);
        Route::get('exports/{id}', [ExportController::class, 'show']);
        Route::get('exports/{id}/download', [ExportController::class, 'download']);
    Route::get('/comparisons/{id}/provider-projection', [ComparisonController::class, 'providerProjection']);
    Route::post('/comparisons/{id}/feedback', [ComparisonController::class, 'submitFeedback']);

    });


    // =========================================
    // ADMIN — Change Lifecycle (WP-13 + WP-13b)
    // =========================================
        // ─── Boost & Payments (S019) ───
    Route::get('boost-packages', [\App\Http\Controllers\Api\V1\BoostController::class, 'packages']);
    Route::post('needs/{needId}/boosts', [\App\Http\Controllers\Api\V1\BoostController::class, 'store'])
        ->middleware('auth:sanctum');
    Route::get('payments/{id}', [\App\Http\Controllers\Api\V1\BoostController::class, 'showPayment'])
        ->middleware('auth:sanctum');

        // ─── Reports, Telegram, Offer Unlock (S021-S023) ───
    Route::post('reports', [\App\Http\Controllers\Api\V1\ReportController::class, 'store'])
        ->middleware('auth:sanctum');
    Route::get('needs/{needId}/telegram-publications', [\App\Http\Controllers\Api\V1\TelegramPublicationController::class, 'index'])
        ->middleware('auth:sanctum');
    Route::post('needs/{needId}/telegram-publication/stop', [\App\Http\Controllers\Api\V1\TelegramPublicationController::class, 'stop'])
        ->middleware('auth:sanctum');
    Route::post('offer-submissions', [\App\Http\Controllers\Api\V1\OfferUnlockController::class, 'store'])
        ->middleware('auth:sanctum');
    Route::get('offer-submissions/{id}', [\App\Http\Controllers\Api\V1\OfferUnlockController::class, 'show'])
        ->middleware('auth:sanctum');
    Route::post('offer-submissions/{id}/resume', [\App\Http\Controllers\Api\V1\OfferUnlockController::class, 'resume'])
        ->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

        // T3 (2026-10-02) — cross-cutting meta endpoints
        Route::get('control-registry', [AdminReadController::class, 'controlRegistry']);
        Route::post('operations', [AdminChangeController::class, 'operations'])
            ->middleware('idempotent');

        Route::prefix('changes')->group(function () {
            // Draft lifecycle (WP-13)
            Route::post('/',                    [AdminChangeController::class, 'store'])
                ->middleware('idempotent');
            Route::get('{id}',                  [AdminChangeController::class, 'show']);
            Route::post('{id}/validate',        [AdminChangeController::class, 'validateDraft']);
            Route::post('{id}/simulate',        [AdminChangeController::class, 'simulate']);
            Route::post('{id}/preview',         [AdminChangeController::class, 'preview']);

            // Publish (WP-13b: reauth + idempotency middleware)
            Route::post('{id}/publish',         [AdminChangeController::class, 'publish'])
                ->middleware(['reauth', 'idempotent']);

            // Apply + Verify (WP-13b: server jobs)
            Route::post('{id}/apply',           [AdminChangeController::class, 'apply'])
                ->middleware('idempotent');
            Route::post('{id}/verify',          [AdminChangeController::class, 'verify'])
                ->middleware('idempotent');

            Route::get('{id}/audit',            [AdminChangeController::class, 'audit']);
            Route::get('{id}/diff',             [AdminChangeController::class, 'diff']);
            Route::post('{id}/rollback',        [AdminChangeController::class, 'rollback'])
                ->middleware('reauth');
            Route::post('{id}/schedule',        [AdminChangeController::class, 'schedule'])
                ->middleware('reauth');
            Route::delete('{id}/schedule',      [AdminChangeController::class, 'unschedule'])
                ->middleware('reauth');
        });

        // Legacy aliases (Auth Contract §HTTP 1.4)
        Route::post('settings/drafts/{id}/publish', [AdminChangeController::class, 'publish'])
            ->middleware(['reauth', 'idempotent']);
        Route::post('settings/{key}/rollback',      [AdminChangeController::class, 'rollback'])
            ->middleware('reauth');

        // WP-05b: Telegram read-only endpoints
        Route::prefix('telegram')->group(function () {
            Route::get('destinations',                 [AdminTelegramController::class, 'destinations']);
            Route::get('destinations/{id}',            [AdminTelegramController::class, 'showDestination']);
            Route::get('publications',                 [AdminTelegramController::class, 'publications']);
            Route::get('publications/{id}',            [AdminTelegramController::class, 'showPublication']);
        });

        // Sponsored Ads admin endpoints (L267)
        Route::prefix('ads')->group(function () {
            Route::get('/', [AdminAdsController::class, 'index']);
            Route::post('advertisers', [AdminAdsController::class, 'storeAdvertiser']);
            Route::post('campaigns', [AdminAdsController::class, 'storeCampaign']);
            Route::get('campaigns/{id}', [AdminAdsController::class, 'showCampaign']);
            Route::patch('campaigns/{id}', [AdminAdsController::class, 'updateCampaign']);
            Route::post('campaigns/{id}/validate', [AdminAdsController::class, 'validateCampaign']);
            Route::post('campaigns/{id}/preview', [AdminAdsController::class, 'previewCampaign']);
            Route::post('campaigns/{id}/publish', [AdminAdsController::class, 'publishCampaign']);
            Route::post('campaigns/{id}/pause', [AdminAdsController::class, 'pauseCampaign']);
            Route::post('campaigns/{id}/resume', [AdminAdsController::class, 'resumeCampaign']);
            Route::post('campaigns/{id}/cancel-schedule', [AdminAdsController::class, 'cancelSchedule']);
            Route::post('campaigns/{id}/archive', [AdminAdsController::class, 'archiveCampaign']);
            Route::post('campaigns/{id}/rollback', [AdminAdsController::class, 'rollbackCampaign']);
            Route::get('campaigns/{id}/reports', [AdminAdsController::class, 'reportCampaign']);
            Route::get('campaigns/{id}/audit', [AdminAdsController::class, 'auditCampaign']);
            Route::post('destinations/validate', [AdminAdsController::class, 'validateDestination']);
            Route::post('creatives/{id}/validate', [AdminAdsController::class, 'validateCreative']);
        });

        // WP-05c: Admin read endpoints (L262)
        Route::get('dashboard',     [AdminReadController::class, 'dashboard']);
        Route::get('telegram-overview', [AdminReadController::class, 'telegram']);
        Route::get('telegram', [AdminReadController::class, 'telegram']); // T3 alias for design path /admin/telegram
        Route::get('health',        [AdminReadController::class, 'health']);
        // L305 — additive: real metrics + health status (do not alter A001/A003 handle())
        Route::get('dashboard-metrics', [AdminReadController::class, 'dashboardMetrics']);
        Route::get('health-status',     [AdminReadController::class, 'healthStatus']);
        // L306 — additive: A012/A013/A014 real status
        Route::get('jobs-status',      [AdminReadController::class, 'jobsStatus']);
        Route::get('backups-status',   [AdminReadController::class, 'backupsStatus']);
        Route::get('integrity-status', [AdminReadController::class, 'integrityStatus']);
        // L307 — additive: A015/A018/A019 real status
        Route::get('security-status',  [AdminReadController::class, 'securityStatus']);
        Route::get('recovery-status',  [AdminReadController::class, 'recoveryStatus']);
        Route::get('safe-mode-status', [AdminReadController::class, 'safeModeStatus']);
        Route::get('settings-status', [AdminReadController::class, 'settingsStatus']);
        Route::get('maintenance-status', [AdminReadController::class, 'maintenanceStatus']);
        Route::get('features-status', [AdminReadController::class, 'featuresStatus']);
        Route::get('monetization-status', [AdminReadController::class, 'monetizationStatus']);
        Route::get('marketplace-status', [AdminReadController::class, 'marketplaceStatus']);
        Route::get('ai-status', [AdminReadController::class, 'aiStatus']);
        Route::get('notifications-status', [AdminReadController::class, 'notificationsStatus']);
        Route::get('features',      [AdminReadController::class, 'features']);
        Route::get('marketplace',   [AdminReadController::class, 'marketplace']);
        Route::get('ai',            [AdminReadController::class, 'ai']);
        Route::get('payments',      [AdminReadController::class, 'payments']);
        Route::get('users',         [AdminReadController::class, 'users']);
        Route::get('content',       [AdminReadController::class, 'content']);
        Route::get('notifications', [AdminReadController::class, 'notifications']);
        Route::get('files',         [AdminReadController::class, 'files']);
        Route::get('jobs',          [AdminReadController::class, 'jobs']);
        Route::get('backups',       [AdminReadController::class, 'backups']);
        Route::get('integrity',     [AdminReadController::class, 'integrity']);
        Route::get('integrity/drift', [AdminReadController::class, 'configDrift']);
        // AM (audit L276) — bulk action safety
        Route::post('bulk/preview',              [BulkActionController::class, 'preview'])
            ->middleware('reauth');
        Route::post('bulk/execute',              [BulkActionController::class, 'execute'])
            ->middleware(['reauth', 'idempotent']);
        Route::post('bulk/{id}/retry-failed',    [BulkActionController::class, 'retryFailed'])
            ->middleware(['reauth', 'idempotent']);
        // AH (audit L276) — safe presets
        Route::get('presets',                    [PresetController::class, 'index']);
        Route::get('presets/{name}/preview',     [PresetController::class, 'preview']);
        Route::post('presets/{name}/apply',      [PresetController::class, 'apply'])
            ->middleware(['reauth', 'idempotent']);
        Route::get('security',      [AdminReadController::class, 'security']);
        Route::get('audit',         [AdminReadController::class, 'audit']);
        Route::get('settings',      [AdminReadController::class, 'settings']);
        Route::get('recovery',      [AdminReadController::class, 'recovery']);
        Route::get('safe-mode',     [AdminReadController::class, 'safeMode']);
        Route::get('monetization',  [AdminReadController::class, 'monetization']);
        Route::get('maintenance',   [AdminReadController::class, 'maintenance']);
        Route::get('reports',       [AdminReadController::class, 'reports']);
        Route::get('controls/{key}/dependencies', [AdminReadController::class, 'controlDependencies']);
    });

});

// Consent API — Proclamation 1321/2024, Art. 7-8
Route::middleware('auth:sanctum')->prefix('v1/consent')->group(function () {
    Route::get('/',        [\App\Http\Controllers\V1\ConsentController::class, 'index']);
    Route::post('/grant',  [\App\Http\Controllers\V1\ConsentController::class, 'grant']);
    Route::post('/revoke', [\App\Http\Controllers\V1\ConsentController::class, 'revoke']);
});

// Data Subject Rights API — Proclamation 1321/2024, Art. 34-39
Route::middleware('auth:sanctum')->prefix('v1/privacy')->group(function () {
    Route::get('/data',       [\App\Http\Controllers\V1\PrivacyController::class, 'show']);      // Art. 34
    Route::patch('/data',     [\App\Http\Controllers\V1\PrivacyController::class, 'update']);    // Art. 35
    Route::delete('/data',    [\App\Http\Controllers\V1\PrivacyController::class, 'destroy']);   // Art. 36
    Route::post('/restrict',  [\App\Http\Controllers\V1\PrivacyController::class, 'restrict']);  // Art. 37
    Route::get('/export',     [\App\Http\Controllers\V1\PrivacyController::class, 'export']);    // Art. 38
    Route::post('/object',    [\App\Http\Controllers\V1\PrivacyController::class, 'object']);    // Art. 39
});

// Email OTP Authentication — Amharic + English
Route::prefix('v1/auth/email')->group(function () {
    Route::post('/request', [\App\Http\Controllers\V1\EmailAuthController::class, 'requestCode']);
    Route::post('/verify',  [\App\Http\Controllers\V1\EmailAuthController::class, 'verifyCode']);
});

// Breach Notification — Proclamation 1321/2024, Art. 30 (admin only)
Route::middleware(['auth:sanctum'])->prefix('v1/admin/breaches')->group(function () {
    Route::get('/',                 [\App\Http\Controllers\V1\BreachController::class, 'index']);
    Route::post('/',                [\App\Http\Controllers\V1\BreachController::class, 'store']);
    Route::get('/overdue',          [\App\Http\Controllers\V1\BreachController::class, 'overdue']);
    Route::get('/{incident}',       [\App\Http\Controllers\V1\BreachController::class, 'show']);
    Route::post('/{incident}/contain',  [\App\Http\Controllers\V1\BreachController::class, 'contain']);
    Route::post('/{incident}/notify-eca',   [\App\Http\Controllers\V1\BreachController::class, 'notifyEca']);
    Route::post('/{incident}/notify-users', [\App\Http\Controllers\V1\BreachController::class, 'notifyUsers']);
    Route::post('/{incident}/resolve',  [\App\Http\Controllers\V1\BreachController::class, 'resolve']);
});

// L304 — Email Verification API
Route::middleware('auth:sanctum')->prefix('v1/auth/verify-email')->group(function () {
    Route::post('/resend', [\App\Http\Controllers\V1\EmailVerificationController::class, 'resend'])
        ->middleware('throttle:5,1');
    Route::get('/status', [\App\Http\Controllers\V1\EmailVerificationController::class, 'status']);
});
