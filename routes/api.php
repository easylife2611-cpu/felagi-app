<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ComparisonController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\NeedController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OfferController;
use App\Http\Controllers\Api\V1\RatingController;
use App\Http\Controllers\Api\V1\Admin\AdminChangeController;
use App\Http\Controllers\Api\V1\Admin\AdminTelegramController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // =========================================
    // AUTH
    // =========================================
    Route::prefix('auth')->group(function () {
        Route::post('telegram/start', [AuthController::class, 'telegramStart'])
            ->middleware('throttle:5,15');

        // Widget flow (BotFather "Web Login" unavailable)
        Route::post('telegram/widget/start', [AuthController::class, 'telegramWidgetStart'])
            ->middleware('throttle:5,15');

        Route::get('telegram/widget/callback', [AuthController::class, 'telegramWidgetCallback'])
            ->middleware('throttle:10,1');

        // WP-27: Telegram redirects user here with ?code=&state=
        Route::get('telegram/callback', [AuthController::class, 'telegramCallback'])
            ->middleware('throttle:10,1');

        // WP-27: Flutter exchanges single-use handoff code for app tokens
        Route::post('telegram/exchange', [AuthController::class, 'telegramExchange'])
            ->middleware('throttle:10,1');
        Route::post('refresh', [AuthController::class, 'refresh']);

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
    });

});
