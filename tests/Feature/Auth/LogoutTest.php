<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * L350 — Logout regression tests.
 *
 * Prevents regression of:
 *   Call to undefined method Laravel\Sanctum\TransientToken::delete()
 *
 * Covers both auth types:
 *   - Sanctum token (API)
 *   - Session auth (web guard — TransientToken)
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logout_with_sanctum_token_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
             ->postJson('/api/v1/auth/logout')
             ->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    /** @test */
    public function logout_with_session_auth_does_not_crash(): void
    {
        // Regression: TransientToken has no delete() method.
        $user = User::factory()->create();

        $this->actingAs($user)
             ->postJson('/api/v1/auth/logout')
             ->assertNoContent();
    }

    /** @test */
    public function logout_without_auth_is_not_500(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');
        $this->assertContains($response->status(), [204, 401]);
    }
}
