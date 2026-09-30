<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S013MyOffersTest extends TestCase
{
    use RefreshDatabase;

    private function makeNeed(User $owner): Need
    {
        $cat = Category::factory()->create(['name_en'=>'L','name_am'=>'ሎ','slug'=>'cat-'.uniqid(),'active'=>true]);
        return Need::factory()->create([
            'category_id' => $cat->id, 'requester_id' => $owner->id,
            'title' => 'Need a truck', 'description' => 'Ref truck needed.',
            'status' => Need::STATUS_OPEN,
        ]);
    }

    public function test_page_renders(): void
    {
        $res = $this->get('/my/offers');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
    }

    public function test_page_has_status_filters(): void
    {
        $res = $this->get('/my/offers');
        foreach (['PENDING','ACCEPTED','REJECTED','WITHDRAWN'] as $s) {
            $res->assertSee('data-status="'.$s.'"', false);
        }
    }

    public function test_api_requires_auth(): void
    {
        $this->getJson('/api/v1/my/offers')->assertStatus(401);
    }

    public function test_api_returns_only_own_offers(): void
    {
        $owner = User::factory()->create();
        $me = User::factory()->create();
        $other = User::factory()->create();
        $need = $this->makeNeed($owner);

        Offer::factory()->create(['need_id' => $need->id, 'provider_id' => $me->id, 'status' => Offer::STATUS_PENDING]);
        Offer::factory()->create(['need_id' => $need->id, 'provider_id' => $other->id, 'status' => Offer::STATUS_PENDING]);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/offers');
        $res->assertStatus(200);
        $this->assertCount(1, $res->json('data'));
    }

    public function test_api_filters_by_status(): void
    {
        $owner = User::factory()->create();
        $me = User::factory()->create();
        $needA = $this->makeNeed($owner);
        $needB = $this->makeNeed($owner);

        Offer::factory()->create(['need_id' => $needA->id, 'provider_id' => $me->id, 'status' => Offer::STATUS_PENDING]);
        Offer::factory()->create(['need_id' => $needB->id, 'provider_id' => $me->id, 'status' => Offer::STATUS_ACCEPTED]);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/offers?status=PENDING');
        $res->assertStatus(200);
        $this->assertCount(1, $res->json('data'));
    }

    public function test_api_paginates(): void
    {
        $owner = User::factory()->create();
        $me = User::factory()->create();
        for ($i = 0; $i < 3; $i++) {
            $need = $this->makeNeed($owner);
            Offer::factory()->create(['need_id' => $need->id, 'provider_id' => $me->id, 'status' => Offer::STATUS_PENDING]);
        }
        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/offers?per_page=2');
        $res->assertStatus(200);
        $res->assertJsonStructure(['data', 'meta' => ['page','per_page','total']]);
    }
}
