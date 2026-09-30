<?php

namespace Tests\Feature\Boost;

use App\Models\BoostPackage;
use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoostControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeNeed(User $owner, string $status = Need::STATUS_OPEN): Need
    {
        $cat = Category::factory()->create(['name_en'=>'L','name_am'=>'ሎ','slug'=>'c'.uniqid(),'active'=>true]);
        return Need::factory()->create([
            'category_id' => $cat->id, 'requester_id' => $owner->id,
            'title' => 'N', 'description' => 'D', 'status' => $status,
        ]);
    }

    public function test_packages_lists_active(): void
    {
        BoostPackage::factory()->create(['active' => true, 'price' => 100]);
        BoostPackage::factory()->create(['active' => false, 'price' => 200]);

        $res = $this->getJson('/api/v1/boost-packages');
        $res->assertStatus(200);
        $this->assertCount(1, $res->json('data'));
    }

    public function test_store_requires_auth(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $this->postJson('/api/v1/needs/'.$need->id.'/boosts', [])->assertStatus(401);
    }

    public function test_owner_can_boost_open_need(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $pkg = BoostPackage::factory()->create(['active' => true, 'price' => 100]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/boosts', ['package_id' => $pkg->id]);

        $res->assertStatus(201);
    }

    public function test_non_owner_cannot_boost(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $need = $this->makeNeed($owner);
        $pkg = BoostPackage::factory()->create(['active' => true]);

        $this->actingAs($other, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/boosts', ['package_id' => $pkg->id])
            ->assertStatus(403);
    }

    public function test_cannot_boost_non_open_need(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner, Need::STATUS_COMPLETED);
        $pkg = BoostPackage::factory()->create(['active' => true]);

        $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/boosts', ['package_id' => $pkg->id])
            ->assertStatus(409);
    }
}
