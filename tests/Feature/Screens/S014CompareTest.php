<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S014CompareTest extends TestCase
{
    use RefreshDatabase;

    private function makeNeed(User $owner): Need
    {
        $cat = Category::factory()->create(['name_en'=>'L','name_am'=>'ሎ','slug'=>'cat-'.uniqid(),'active'=>true]);
        return Need::factory()->create([
            'category_id' => $cat->id, 'requester_id' => $owner->id,
            'title' => 'Need', 'description' => 'Ref.', 'status' => Need::STATUS_OPEN,
        ]);
    }

    public function test_page_renders(): void
    {
        $res = $this->get('/needs/abc/compare');
        $res->assertStatus(200);
        $res->assertSee('id="select-all-chk"', false);
        $res->assertSee('id="evaluate-btn"', false);
    }

    public function test_page_has_all_states(): void
    {
        $res = $this->get('/needs/abc/compare');
        foreach (['state-loading','state-empty','state-error','content'] as $id) {
            $res->assertSee('id="'.$id.'"', false);
        }
    }

    public function test_comparison_requires_auth(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);

        $res = $this->postJson('/api/v1/needs/'.$need->id.'/comparisons', ['offer_ids' => []]);
        $res->assertStatus(401);
    }

    public function test_comparison_requires_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $need = $this->makeNeed($owner);

        $res = $this->actingAs($other, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/comparisons', ['offer_ids' => []]);
        $res->assertStatus(403);
    }

    public function test_comparison_requires_open_need(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $need->update(['status' => Need::STATUS_COMPLETED]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/comparisons', ['offer_ids' => []]);
        $res->assertStatus(409);
    }

    public function test_comparison_returns_501_when_ai_not_configured(): void
    {
        $owner = User::factory()->create();
        $provider1 = User::factory()->create();
        $provider2 = User::factory()->create();
        $need = $this->makeNeed($owner);

        $o1 = Offer::factory()->create(['need_id' => $need->id, 'provider_id' => $provider1->id, 'status' => Offer::STATUS_PENDING]);
        $o2 = Offer::factory()->create(['need_id' => $need->id, 'provider_id' => $provider2->id, 'status' => Offer::STATUS_PENDING]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/comparisons', ['offer_ids' => [$o1->id, $o2->id]]);

        $res->assertStatus(501);
    }

    public function test_comparison_history_requires_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $need = $this->makeNeed($owner);

        $res = $this->actingAs($other, 'sanctum')->getJson('/api/v1/needs/'.$need->id.'/comparisons');
        $res->assertStatus(403);
    }

    public function test_comparison_history_returns_for_owner(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);

        $res = $this->actingAs($owner, 'sanctum')->getJson('/api/v1/needs/'.$need->id.'/comparisons');
        $res->assertStatus(200);
    }
}
