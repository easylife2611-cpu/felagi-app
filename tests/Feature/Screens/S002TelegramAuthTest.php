<?php
namespace Tests\Feature\Screens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S002TelegramAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_start_route_exists(): void
    {
        $this->postJson('/api/v1/auth/telegram/widget/start', [])
            ->assertStatus(422);
    }

    public function test_widget_callback_route_exists(): void
    {
        $res = $this->getJson('/api/v1/auth/telegram/widget/callback');
        // Without proper payload → 400 (bad request) or 422 (validation)
        $this->assertContains($res->status(), [400, 422]);
    }

    public function test_logout_requires_auth(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertStatus(401);
    }
}
