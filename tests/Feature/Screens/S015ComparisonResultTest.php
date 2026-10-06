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
    public function test_provider_only_receives_own_snapshot_and_result(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $need = \App\Models\Need::factory()->create(['requester_id' => $owner->id]);
        $comparison = \App\Models\Comparison::factory()->create(['need_id' => $need->id]);
        $own = \App\Models\ComparisonOffer::factory()->create([
            'comparison_id' => $comparison->id, 'provider_id' => $provider->id,
        ]);
        $other = \App\Models\ComparisonOffer::factory()->create(['comparison_id' => $comparison->id]);
        foreach ([$own, $other] as $snapshot) {
            \App\Models\ComparisonResult::factory()->create([
                'comparison_id' => $comparison->id, 'comparison_offer_id' => $snapshot->id,
            ]);
        }
        $this->actingAs($provider, 'sanctum')->getJson('/api/v1/comparisons/'.$comparison->id)
            ->assertOk()->assertJsonCount(1, 'data.results')
            ->assertJsonCount(1, 'data.comparison_offers')
            ->assertJsonPath('data.results.0.comparison_offer_id', $own->id)
            ->assertJsonMissing(['comparison_offer_id' => $other->id]);
        $this->getJson('/api/v1/comparisons/'.$comparison->id.'/results')
            ->assertOk()->assertJsonCount(1, 'data.results')
            ->assertJsonPath('data.results.0.comparison_offer_id', $own->id);
        $this->actingAs($owner, 'sanctum')->getJson('/api/v1/comparisons/'.$comparison->id)
            ->assertOk()->assertJsonCount(2, 'data.results');
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/v1/comparisons/'.$comparison->id.'/results')->assertNotFound();
    }
}
