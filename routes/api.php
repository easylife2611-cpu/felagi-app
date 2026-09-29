<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\NeedController;
use App\Http\Controllers\Api\V1\OfferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
| Base path: /api/v1
| All routes return JSON via BaseApiController envelope.
| See DFM-FDS-1.4.md §5.2 for canonical route register.
*/

Route::prefix('v1')->group(function () {

    // =========================================
    // AUTH (public)
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
    // CATEGORIES (public)
    // =========================================
    Route::get('categories', [CategoryController::class, 'index']);

    // =========================================
    // NEEDS (mixed)
    // =========================================
    // Public listing
    Route::get('needs', [NeedController::class, 'index'])
        ->middleware('throttle:120,1');

    // Single Need (public or owner projection)
    Route::get('needs/{id}', [NeedController::class, 'show']);

    // Authenticated Need operations
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('needs', [NeedController::class, 'store'])
            ->middleware('throttle:10,60');
        Route::put('needs/{id}', [NeedController::class, 'update']);
        Route::post('needs/{id}/cancel', [NeedController::class, 'cancel']);
        Route::post('needs/{id}/complete', [NeedController::class, 'complete']);

        // My resources
        Route::get('my/needs', [NeedController::class, 'myNeeds']);
        Route::get('my/offers', [OfferController::class, 'myOffers']);
    });

    // =========================================
    // OFFERS (mixed)
    // =========================================
    Route::middleware('auth:sanctum')->group(function () {
        // Offers on a Need (owner view)
        Route::get('needs/{needId}/offers', [OfferController::class, 'index']);

        // Submit Offer
        Route::post('needs/{needId}/offers', [OfferController::class, 'store'])
            ->middleware('throttle:10,60');

        // Single Offer operations
        Route::get('offers/{id}', [OfferController::class, 'show']);
        Route::put('offers/{id}', [OfferController::class, 'update']);
        Route::post('offers/{id}/withdraw', [OfferController::class, 'withdraw']);
        Route::post('offers/{id}/reject', [OfferController::class, 'reject']);
        Route::post('offers/{id}/accept', [OfferController::class, 'accept']);
    });

    // =========================================
    // PROFILE (authenticated)
    // =========================================
    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('profile', [AuthController::class, 'updateProfile']);
    });

});
