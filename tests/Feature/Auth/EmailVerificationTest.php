<?php

namespace Tests\Feature\Auth;

use App\Mail\VerifyEmailMail;
use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    public function test_service_sends_verification_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'verify@example.com']);
        $service = app(EmailVerificationService::class);
        $service->send($user);

        Mail::assertSent(VerifyEmailMail::class);
        $this->assertDatabaseHas('email_verification_tokens', ['user_id' => $user->id]);
    }

    public function test_service_verifies_valid_token(): void
    {
        $user = User::factory()->create();
        $service = app(EmailVerificationService::class);

        $plaintext = 'abc123def456';
        EmailVerificationToken::create([
            'user_id'    => $user->id,
            'token_hash' => EmailVerificationToken::hashToken($plaintext),
            'expires_at' => now()->addHours(24),
        ]);

        $verified = $service->verify($plaintext);
        $this->assertNotNull($verified);
        $this->assertEquals($user->id, $verified->id);
        $this->assertNotNull($verified->email_verified_at);
    }

    public function test_service_rejects_expired_token(): void
    {
        $user = User::factory()->create();
        $service = app(EmailVerificationService::class);

        $plaintext = 'expired-token';
        EmailVerificationToken::create([
            'user_id'    => $user->id,
            'token_hash' => EmailVerificationToken::hashToken($plaintext),
            'expires_at' => now()->subHour(),
        ]);

        $this->assertNull($service->verify($plaintext));
    }

    public function test_service_rejects_consumed_token(): void
    {
        $user = User::factory()->create();
        $service = app(EmailVerificationService::class);

        $plaintext = 'used-token';
        EmailVerificationToken::create([
            'user_id'     => $user->id,
            'token_hash'  => EmailVerificationToken::hashToken($plaintext),
            'expires_at'  => now()->addHours(24),
            'consumed_at' => now(),
        ]);

        $this->assertNull($service->verify($plaintext));
    }

    public function test_service_reports_unverified(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $service = app(EmailVerificationService::class);
        $this->assertFalse($service->isVerified($user));
    }

    public function test_service_reports_verified(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $service = app(EmailVerificationService::class);
        $this->assertTrue($service->isVerified($user));
    }

    public function test_web_verify_route_works(): void
    {
        $user = User::factory()->create();
        $service = app(EmailVerificationService::class);

        $plaintext = 'web-test-token';
        EmailVerificationToken::create([
            'user_id'    => $user->id,
            'token_hash' => EmailVerificationToken::hashToken($plaintext),
            'expires_at' => now()->addHours(24),
        ]);

        $this->get('/verify-email/' . $plaintext)
            ->assertRedirect('/browse');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_web_verify_with_invalid_token_redirects_with_error(): void
    {
        $this->get('/verify-email/invalidtoken123')
            ->assertRedirect('/?verify_error=1');
    }

    public function test_api_resend_requires_auth(): void
    {
        $this->postJson('/api/v1/auth/verify-email/resend')->assertUnauthorized();
    }

    public function test_api_resend_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/verify-email/resend')
            ->assertOk();

        Mail::assertSent(VerifyEmailMail::class);
    }

    public function test_api_resend_fails_if_already_verified(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/verify-email/resend')
            ->assertStatus(409);
    }

    public function test_api_status_reports_state(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/verify-email/status')
            ->assertOk()
            ->assertJsonPath('data.verified', false);
    }
}
