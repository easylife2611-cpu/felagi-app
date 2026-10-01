<?php

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * S001 Welcome — professional Email-first login (L302)
 *
 * Primary: Email OTP
 * Secondary: Telegram Widget
 */
class S001WelcomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_renders(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_welcome_has_brand_and_purpose(): void
    {
        $this->get('/')
            ->assertSee('felagi-lockup', false)
            ->assertSee("Ethiopia's need-first marketplace", false);
    }

    public function test_welcome_has_email_input(): void
    {
        $res = $this->get('/');
        $res->assertStatus(200)
            ->assertSee('id="email-input"', false)
            ->assertSee('your@email.com', false);
    }

    public function test_welcome_has_continue_with_email_button(): void
    {
        $this->get('/')
            ->assertSee('id="email-continue"', false)
            ->assertSee('Continue with Email', false);
    }

    public function test_welcome_has_otp_view(): void
    {
        $res = $this->get('/');
        $res->assertStatus(200)
            ->assertSee('id="view-otp"', false)
            ->assertSee('id="otp-inputs"', false)
            ->assertSee('id="otp-verify"', false)
            ->assertSee('Check your email', false);
    }

    public function test_welcome_has_resend_button(): void
    {
        $this->get('/')
            ->assertSee('id="resend-btn"', false)
            ->assertSee('Resend code', false);
    }

    public function test_welcome_has_telegram_as_secondary_option(): void
    {
        $res = $this->get('/');
        $res->assertStatus(200)
            ->assertSee('id="telegram-continue"', false)
            ->assertSee('Continue with Telegram', false)
            ->assertSee('id="telegram-widget-container"', false);
    }

    public function test_welcome_has_or_divider(): void
    {
        $this->get('/')->assertSee('class="divider"', false);
    }

    public function test_welcome_has_terms_and_privacy_links(): void
    {
        $this->get('/')
            ->assertSee('TERMS_OF_SERVICE', false)
            ->assertSee('PRIVACY_POLICY', false);
    }

    public function test_welcome_has_csrf_token(): void
    {
        $this->get('/')->assertSee('csrf-token', false);
    }

    public function test_welcome_does_not_use_oidc_link(): void
    {
        // L296/L302 — OIDC reverted; Email OTP + Telegram Widget are used.
        $this->get('/')->assertDontSee('oidc/start', false);
    }

    public function test_welcome_renders_in_amharic(): void
    {
        $this->withHeaders(['Accept-Language' => 'am'])
            ->get('/')
            ->assertOk();
    }
}
