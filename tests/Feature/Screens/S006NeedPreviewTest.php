<?php

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S006NeedPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $res = $this->get('/needs/new/public-preview');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
        $res->assertSee('id="content"', false);
    }

    public function test_page_has_publish_button(): void
    {
        $res = $this->get('/needs/new/public-preview');
        $res->assertSee('id="btn-publish"', false);
        $res->assertSee('id="btn-edit"', false);
    }

    public function test_page_has_draft_badge(): void
    {
        $res = $this->get('/needs/new/public-preview');
        $res->assertSee('class="draft-badge"', false);
    }
}
