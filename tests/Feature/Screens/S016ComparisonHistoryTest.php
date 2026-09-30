<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S016ComparisonHistoryTest extends TestCase
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
        $res = $this->get('/needs/abc-123/comparisons');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
        $res->assertSee('id="list"', false);
    }

    public function test_page_has_empty_and_error_states(): void
    {
        $res = $this->get('/needs/abc-123/comparisons');
        $res->assertSee('id="state-empty"', false);
        $res->assertSee('id="state-error"', false);
    }

    public function test_history_requires_auth(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $res = $this->getJson('/api/v1/needs/'.$need->id.'/comparisons');
        $res->assertStatus(401);
    }

    public function test_history_requires_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $need = $this->makeNeed($owner);
        $res = $this->actingAs($other, 'sanctum')->getJson('/api/v1/needs/'.$need->id.'/comparisons');
        $res->assertStatus(403);
    }

    public function test_history_returns_for_owner(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $res = $this->actingAs($owner, 'sanctum')->getJson('/api/v1/needs/'.$need->id.'/comparisons');
        $res->assertStatus(200);
    }
}
