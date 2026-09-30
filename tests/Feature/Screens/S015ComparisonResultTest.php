<?php

namespace Tests\Feature\Screens;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S015ComparisonResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $res = $this->get('/comparisons/abc-123');
        $res->assertStatus(200);
        $res->assertSee('id="status-pill"', false);
        $res->assertSee('id="version-number"', false);
    }

    public function test_page_has_results_and_offers_sections(): void
    {
        $res = $this->get('/comparisons/abc-123');
        $res->assertSee('id="results-card"', false);
        $res->assertSee('id="offers-card"', false);
        $res->assertSee('id="ai-note"', false);
    }

    public function test_comparison_requires_auth(): void
    {
        $res = $this->getJson('/api/v1/comparisons/abc-123');
        $res->assertStatus(401);
    }

    public function test_comparison_not_found_returns_404(): void
    {
        $user = User::factory()->create();
        $fakeUuid = '00000000-0000-0000-0000-000000000000';
        $res = $this->actingAs($user, 'sanctum')->getJson('/api/v1/comparisons/'.$fakeUuid);
        $res->assertStatus(404);
    }
}
