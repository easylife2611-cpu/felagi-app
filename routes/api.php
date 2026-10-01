<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdsDeliveryController;
use App\Http\Controllers\Api\V1\AdsEventController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\ComparisonController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\NeedController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OfferController;
use App\Http\Controllers\Api\V1\RatingController;
use App\Http\Controllers\Api\V1\TwoFactorController;
use App\Http\Controllers\Api\V1\Admin\AdminChangeController;
use App\Http\Controllers\Api\V1\Admin\AdminTelegramController;
use App\Http\Controllers\Api\V1\Admin\AdminReadController;
use App\Http\Controllers\Api\V1\Admin\BulkActionController;
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

        // Needs
        Route::post('needs', [NeedController::class, 'store'])
            ->middleware('throttle:10,60');
        Route::put('needs/{id}', [NeedController::class, 'update']);
        Route::post('needs/{id}/cancel', [NeedController::class, 'cancel']);
        Route::post('needs/{id}/complete', [NeedController::class, 'complete']);

        // My resources
        Route::get('my/needs', [NeedController::class, 'myNeeds']);
        Route::get('my/offers', [OfferController::class, 'myOffers']);

        // Offers
        Route::get('needs/{needId}/offers', [OfferController::class, 'index']);
        Route::post('needs/{needId}/offers', [OfferController::class, 'store'])
            ->middleware('throttle:10,60');
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
        Route::get('health',        [AdminReadController::class, 'health']);
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
