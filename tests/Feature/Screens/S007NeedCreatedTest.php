<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S007NeedCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $res = $this->get('/needs/abc-123/created');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
        $res->assertSee('id="content"', false);
    }

    public function test_page_has_next_steps_and_actions(): void
    {
        $res = $this->get('/needs/abc-123/created');
        $res->assertSee('id="btn-view"', false);
        $res->assertSee('id="btn-offers"', false);
        $res->assertSee('class="next-list"', false);
    }

    public function test_page_has_create_another_link(): void
    {
        $res = $this->get('/needs/abc-123/created');
        $res->assertSee('href="/browse"', false);
    }
}
