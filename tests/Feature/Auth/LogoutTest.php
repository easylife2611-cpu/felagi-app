<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
    public function logout_with_session_auth_returns_204(): void
    {
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

    /**
     * Regression: TransientToken::delete() crash
     *
     * This test previously caused a fatal error when session-based
     * auth was used (Sanctum returns TransientToken which has no
     * delete() method). The fix uses instanceof PersonalAccessToken
     * to only revoke real tokens.
     *
     * @test
     */
    public function session_auth_does_not_trigger_transient_token_crash(): void
    {
        $user = User::factory()->create();

        // Session-based auth — Sanctum returns TransientToken
        $this->actingAs($user, 'web');

        // Should return 204, NOT 500
        $response = $this->postJson('/api/v1/auth/logout');

        $this->assertSame(204, $response->status(),
            'Logout must not crash with session auth (TransientToken bug)');
    }
}
