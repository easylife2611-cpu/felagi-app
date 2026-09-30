<?php

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S023OfferUnlockTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $res = $this->get('/needs/abc-123/offers/unlock');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
        $res->assertSee('id="content"', false);
    }

    public function test_page_has_unlock_button(): void
    {
        $res = $this->get('/needs/abc-123/offers/unlock');
        $res->assertSee('id="unlock-btn"', false);
        $res->assertSee('id="cancel-btn"', false);
    }

    public function test_page_has_pending_notice_and_price(): void
    {
        $res = $this->get('/needs/abc-123/offers/unlock');
        $res->assertSee('id="api-warn"', false);
        $res->assertSee('id="unlock-price"', false);
    }

    public function test_page_has_benefits_section(): void
    {
        $res = $this->get('/needs/abc-123/offers/unlock');
        $res->assertSee('id="unlock-rows"', false);
    }
}
