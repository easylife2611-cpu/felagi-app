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
        Route::post('telegram/exchange', [AuthController::class, 'telegramExchange']);
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
    // ADMIN — Change Lifecycle (WP-13)
    // =========================================
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

        Route::prefix('changes')->group(function () {
            Route::post('/',                    [AdminChangeController::class, 'store']);
            Route::get('{id}',                  [AdminChangeController::class, 'show']);
            Route::post('{id}/validate',        [AdminChangeController::class, 'validateDraft']);
            Route::post('{id}/simulate',        [AdminChangeController::class, 'simulate']);
            Route::post('{id}/preview',         [AdminChangeController::class, 'preview']);
            Route::post('{id}/publish',         [AdminChangeController::class, 'publish']);
            Route::get('{id}/audit',            [AdminChangeController::class, 'audit']);
            Route::post('{id}/rollback',        [AdminChangeController::class, 'rollback']);
        });

        // Legacy aliases (Auth Contract §HTTP 1.4)
        Route::post('settings/drafts/{id}/publish', [AdminChangeController::class, 'publish']);
        Route::post('settings/{key}/rollback',      [AdminChangeController::class, 'rollback']);
    });

});
