<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Services\AI\NullGeminiClient;
use Tests\TestCase;

final class NullGeminiClientTest extends TestCase
{
    public function test_generate_structured_returns_stub(): void
    {
        $client = new NullGeminiClient();

        $result = $client->generateStructured(
            responseSchema: ['type' => 'object'],
            contents: [['parts' => [['text' => 'hello']]]],
        );

        $this->assertIsArray($result);
        $this->assertNull($result['error']);
        $this->assertTrue($result['json']['stub']);
        $this->assertSame('null', $result['json']['driver']);
        $this->assertSame('hello', $result['json']['echo']);
        $this->assertSame(0, $result['usage']['totalTokenCount']);
    }

    public function test_post_is_deterministic(): void
    {
        $client = new NullGeminiClient();
        $payload = [
            'generationConfig' => ['response_schema' => ['type' => 'object']],
            'contents'         => [['parts' => [['text' => 'same']]]],
        ];

        $a = $client->post($payload);
        $b = $client->post($payload);

        $this->assertSame($a['raw'], $b['raw']);
    }

    public function test_null_client_has_no_credentials_requirement(): void
    {
        // Should not throw even with empty API key
        config(['ai.gemini.api_key' => null]);

        $client = new NullGeminiClient();
        $result = $client->generateStructured(['type' => 'object'], []);

        $this->assertNull($result['error']);
    }
}
