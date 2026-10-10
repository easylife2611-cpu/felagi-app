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
        $res->assertSee('class="badge"', false);
    }

    public function test_page_has_empty_state(): void
    {
        $res = $this->get('/needs/new/public-preview');
        $res->assertSee('id="state-empty"', false);
    }

    public function test_page_has_meta_grid_and_back_button(): void
    {
        $res = $this->get('/needs/new/public-preview');
        $res->assertSee('id="need-meta"', false);
        $res->assertSee('id="btn-edit"', false);
    }

    public function test_page_has_actions_hidden_by_default(): void
    {
        $res = $this->get('/needs/new/public-preview');
        // The actions bar is only shown once a draft is loaded from
        // localStorage; the server-rendered HTML has it hidden.
        $res->assertSee('id="actions"', false);
    }

    public function test_page_has_toast_element(): void
    {
        $res = $this->get('/needs/new/public-preview');
        $res->assertSee('id="toast"', false);
    }
}
