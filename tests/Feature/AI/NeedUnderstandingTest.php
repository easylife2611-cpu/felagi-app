<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Models\User;
use App\Services\AI\GeminiClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NeedUnderstandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_understand_requires_authentication(): void
    {
        $this->postJson('/api/v1/needs/understand', ['text' => 'I need a truck'])->assertStatus(401);
    }

    public function test_understand_returns_structured_draft_without_creating_need(): void
    {
        $user = User::factory()->create();
        $this->mock(GeminiClient::class, function ($mock): void {
            $mock->shouldReceive('generateStructured')->once()->andReturn([
                'json' => [
                    'title' => 'Refrigerated truck',
                    'description' => 'A refrigerated truck is needed.',
                    'required' => ['refrigerated truck'],
                    'preferred' => [],
                    'optional' => [],
                    'unknown' => ['budget'],
                    'clarifying_questions' => ['What is your budget?'],
                ],
                'raw' => '{}', 'usage' => [], 'error' => null,
            ]);
        });

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs/understand', [
            'text' => 'I need a refrigerated truck to Adama.',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Refrigerated truck')
            ->assertJsonPath('data.user_confirmation_required', true)
            ->assertJsonPath('data.original_text', 'I need a refrigerated truck to Adama.');

        $this->assertDatabaseCount('needs', 0);
    }

    public function test_understand_returns_503_when_ai_fails(): void
    {
        $user = User::factory()->create();
        $this->mock(GeminiClient::class, function ($mock): void {
            $mock->shouldReceive('generateStructured')->once()->andReturn([
                'json' => null, 'raw' => '', 'usage' => [], 'error' => 'HTTP 503: unavailable',
            ]);
        });

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/needs/understand', ['text' => 'I need a service'])
            ->assertStatus(503)
            ->assertJsonPath('error.code', 'AI_NEED_UNDERSTANDING_FAILED');
    }
}
