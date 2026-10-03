<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AI\GeminiClient;
use App\Services\AI\GeminiClientFactory;
use Illuminate\Support\ServiceProvider;

final class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GeminiClientFactory::class, function (): GeminiClientFactory {
            return new GeminiClientFactory();
        });

        // Auto-select real vs null based on AI_DRIVER / API key presence.
        // ComparisonService and other auto-wired callers get the right one.
        $this->app->singleton(GeminiClient::class, function ($app): GeminiClient {
            return $app->make(GeminiClientFactory::class)->make();
        });
    }
}
