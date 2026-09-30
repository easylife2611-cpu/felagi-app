<?php

namespace Tests\Feature\Offer;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferUnlockTest extends TestCase
{
    use RefreshDatabase;

    private function makeNeed(User $owner): Need
    {
        $cat = Category::factory()->create(['name_en'=>'L','name_am'=>'ሎ','slug'=>'c'.uniqid(),'active'=>true]);
        return Need::factory()->create([
            'category_id' => $cat->id, 'requester_id' => $owner->id,
            'title' => 'N', 'description' => 'D', 'status' => Need::STATUS_OPEN,
        ]);
    }

    public function test_requires_auth(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $this->postJson('/api/v1/offer-submissions', ['need_id' => $need->id])->assertStatus(401);
    }

    public function test_provider_creates_submission(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $need = $this->makeNeed($owner);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', ['need_id' => $need->id]);

        $res->assertStatus(201);
        $this->assertDatabaseHas('offer_submissions', ['need_id' => $need->id, 'provider_id' => $provider->id]);
    }

    public function test_owner_cannot_unlock_own_need(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/offer-submissions', ['need_id' => $need->id])
            ->assertStatus(403);
    }

    public function test_duplicate_returns_existing(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $need = $this->makeNeed($owner);

        $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', ['need_id' => $need->id]);
        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', ['need_id' => $need->id]);
        $res->assertStatus(200);
    }

    public function test_show_requires_owner(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $other = User::factory()->create();
        $need = $this->makeNeed($owner);

        $create = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', ['need_id' => $need->id]);
        $id = $create->json('data.id');

        $this->actingAs($other, 'sanctum')->getJson('/api/v1/offer-submissions/'.$id)->assertStatus(403);
    }
}
