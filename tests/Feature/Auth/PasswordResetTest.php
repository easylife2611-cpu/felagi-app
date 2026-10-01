<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Services\Auth\PasswordResetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    public function test_service_sends_reset_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'reset@example.com']);
        $service = app(PasswordResetService::class);
        $token = $service->send($user);

        Mail::assertSent(PasswordResetMail::class);
        $this->assertNotEmpty($token);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_service_verifies_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $service = app(PasswordResetService::class);
        $token = $service->send($user);

        $verified = $service->verify($user->email, $token);
        $this->assertNotNull($verified);
        $this->assertEquals($user->id, $verified->id);
    }

    public function test_service_rejects_wrong_token(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $service = app(PasswordResetService::class);
        $service->send($user);

        $this->assertNull($service->verify($user->email, 'wrong-token'));
    }

    public function test_service_resets_password(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $service = app(PasswordResetService::class);
        $token = $service->send($user);

        $updated = $service->reset($user->email, $token, 'new-password-1234');
        $this->assertNotNull($updated);
        $this->assertTrue(Hash::check('new-password-1234', $updated->password));

        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_web_forgot_password_page_renders(): void
    {
        $this->get('/forgot-password')->assertOk()->assertSee('Forgot Password', false);
    }

    public function test_web_forgot_password_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'user@example.com']);

        $this->postJson('/forgot-password', ['email' => $user->email])
            ->assertOk();

        Mail::assertSent(PasswordResetMail::class);
    }

    public function test_web_forgot_password_silent_for_unknown_email(): void
    {
        Mail::fake();

        $this->postJson('/forgot-password', ['email' => 'unknown@example.com'])
            ->assertOk();

        Mail::assertNothingSent();
    }

    public function test_web_reset_password_page_renders(): void
    {
        $this->get('/reset-password/faketoken?email=test@example.com')
            ->assertOk()
            ->assertSee('New Password', false);
    }

    public function test_web_reset_password_works(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $service = app(PasswordResetService::class);
        $token = $service->send($user);

        $this->postJson('/reset-password', [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => 'new-password-1234',
            'password_confirmation' => 'new-password-1234',
        ])->assertOk();

        $this->assertTrue(Hash::check('new-password-1234', $user->fresh()->password));
    }

    public function test_web_reset_password_rejects_invalid_token(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $this->postJson('/reset-password', [
            'email'                 => $user->email,
            'token'                 => 'invalid-token',
            'password'              => 'new-password-1234',
            'password_confirmation' => 'new-password-1234',
        ])->assertStatus(422);
    }

    public function test_web_reset_password_validates_minimum_length(): void
    {
        $this->postJson('/reset-password', [
            'email'                 => 'test@example.com',
            'token'                 => 'whatever',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ])->assertStatus(422);
    }
}
