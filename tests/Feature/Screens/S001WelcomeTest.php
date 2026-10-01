<?php
namespace Tests\Feature\Screens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * S001 Welcome — L296 version (widget restored).
 */
class S001WelcomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_renders(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_welcome_has_sign_in_button(): void
    {
        $res = $this->get('/');
        $res->assertStatus(200)
            ->assertSee('id="signin-btn"', false)
            ->assertSee('startTelegramSignIn', false);
    }

    public function test_welcome_has_widget_container(): void
    {
        $this->get('/')->assertSee('id="widget-container"', false);
    }

    public function test_welcome_has_telegram_widget_loader(): void
    {
        $res = $this->get('/');
        $res->assertStatus(200)
            ->assertSee('telegram-widget.js', false)
            ->assertSee('data-onauth', false);
    }

    public function test_welcome_has_iframe_title_observer(): void
    {
        $this->get('/')->assertSee('s002-widget-title-observer', false);
    }

    public function test_welcome_does_not_use_oidc_link(): void
    {
        // L296 — OIDC approach reverted; widget is back.
        $this->get('/')->assertDontSee('/auth/telegram/oidc/start', false);
    }
}
