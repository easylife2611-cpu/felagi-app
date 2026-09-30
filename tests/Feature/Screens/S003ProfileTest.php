<?php
namespace Tests\Feature\Screens;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S003ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_renders(): void
    {
        $res = $this->get('/profile');
        $res->assertStatus(200);
        $res->assertSee('id="profile-form"', false);
    }

    public function test_profile_page_has_photo_upload(): void
    {
        $this->get('/profile')->assertSee('id="photo-input"', false);
    }

    public function test_me_requires_auth(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_me_returns_user_when_authed(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u, 'sanctum')->getJson('/api/v1/auth/me')->assertStatus(200);
    }

    public function test_profile_update_requires_auth(): void
    {
        $this->patchJson('/api/v1/profile', ['full_name' => 'Test'])->assertStatus(401);
    }

    public function test_profile_update_works(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u, 'sanctum')
            ->patchJson('/api/v1/profile', ['full_name' => 'New Name'])
            ->assertStatus(200);
    }
}
