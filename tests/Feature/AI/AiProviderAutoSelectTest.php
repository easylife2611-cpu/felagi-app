<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Services\AI\GeminiClient;
use App\Services\AI\GeminiClientFactory;
use App\Services\AI\NullGeminiClient;
use Tests\TestCase;

final class AiProviderAutoSelectTest extends TestCase
{
    public function test_factory_returns_null_when_no_api_key(): void
    {
        config(['ai.gemini.api_key' => null]);

        $client = (new GeminiClientFactory())->make('auto');

        $this->assertInstanceOf(NullGeminiClient::class, $client);
    }

    public function test_factory_returns_real_when_api_key_set(): void
    {
        config(['ai.gemini.api_key' => 'test-key-not-used']);

        $client = (new GeminiClientFactory())->make('auto');

        $this->assertInstanceOf(GeminiClient::class, $client);
        $this->assertNotInstanceOf(NullGeminiClient::class, $client);
    }

    public function test_factory_explicit_null(): void
    {
        $client = (new GeminiClientFactory())->make('null');
        $this->assertInstanceOf(NullGeminiClient::class, $client);
    }

    public function test_factory_throws_on_unknown_driver(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new GeminiClientFactory())->make('unknown');
    }

    public function test_container_binding_resolves_to_null_in_test_env(): void
    {
        // .env.testing has no GEMINI_API_KEY
        config(['ai.gemini.api_key' => null]);

        // Re-bind to simulate fresh container resolution
        $this->app->forgetInstance(GeminiClient::class);
        $client = app(GeminiClient::class);

        $this->assertInstanceOf(NullGeminiClient::class, $client);
    }
}
