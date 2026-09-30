<?php

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S022TelegramPublicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $res = $this->get('/needs/abc-123/publications');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
        $res->assertSee('id="content"', false);
    }

    public function test_page_has_all_states(): void
    {
        $res = $this->get('/needs/abc-123/publications');
        foreach (['state-loading','state-empty','state-error','content'] as $id) {
            $res->assertSee('id="'.$id.'"', false);
        }
    }

    public function test_page_has_pending_notice(): void
    {
        $res = $this->get('/needs/abc-123/publications');
        $res->assertSee('id="api-warn"', false);
    }
}
