<?php

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S019BoostTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $res = $this->get('/needs/abc-123/boost');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
        $res->assertSee('id="packages-list"', false);
    }

    public function test_page_has_boost_and_cancel_buttons(): void
    {
        $res = $this->get('/needs/abc-123/boost');
        $res->assertSee('id="boost-btn"', false);
        $res->assertSee('id="cancel-btn"', false);
    }

    public function test_page_has_pending_notice(): void
    {
        $res = $this->get('/needs/abc-123/boost');
        $res->assertSee('id="api-warn"', false);
    }

    public function test_page_has_error_state(): void
    {
        $res = $this->get('/needs/abc-123/boost');
        $res->assertSee('id="state-error"', false);
    }

    public function test_page_has_offline_banner(): void
    {
        $res = $this->get('/needs/abc-123/boost');
        $res->assertSee('id="offline-banner"', false);
    }

    public function test_page_has_back_button(): void
    {
        $res = $this->get('/needs/abc-123/boost');
        $res->assertSee('id="back-btn"', false);
    }

    public function test_boost_button_starts_disabled(): void
    {
        $res = $this->get('/needs/abc-123/boost');
        // No package selected yet — button is disabled in the initial HTML
        $res->assertSee('id="boost-btn" disabled', false);
    }
}
