<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S010ReceivedOffersTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategory(array $o = []): Category
    {
        return Category::factory()->create(array_merge([
            'name_en' => 'Logistics',
            'name_am' => 'ሎጂስቲክስ',
            'slug'    => 'cat-' . uniqid(),
            'active'  => true,
        ], $o));
    }

    private function makeUser(array $o = []): User
    {
        return User::factory()->create($o);
    }

    private function makeNeed(Category $cat, User $req, array $o = []): Need
    {
        return Need::factory()->create(array_merge([
            'category_id'  => $cat->id,
            'requester_id' => $req->id,
            'title'        => 'Need a truck to Adama',
            'description'  => 'Looking for a refrigerated truck.',
            'location_text'=> 'Addis Ababa',
            'budget_min'   => 5000,
            'budget_max'   => 8000,
            'currency'     => 'ETB',
            'status'       => Need::STATUS_OPEN,
        ], $o));
    }

    private function makeOffer(Need $need, User $provider, array $o = []): Offer
    {
        return Offer::factory()->create(array_merge([
            'need_id'          => $need->id,
            'provider_id'      => $provider->id,
            'offered_price'    => 7500,
            'currency'         => 'ETB',
            'proposal_message' => 'I can provide a refrigerated truck within 3 days.',
            'status'           => Offer::STATUS_PENDING,
        ], $o));
    }

    public function test_received_offers_page_renders(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->get('/needs/' . $need->id . '/offers');
        $res->assertStatus(200);
        $res->assertSee('id="state-list"', false);
    }

    public function test_received_offers_page_has_all_states(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->get('/needs/' . $need->id . '/offers');
        foreach (['state-loading','state-denied','state-error','state-empty','content'] as $id) {
            $res->assertSee('id="' . $id . '"', false);
        }
    }

    public function test_received_offers_page_has_compare_button(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->get('/needs/' . $need->id . '/offers');
        $res->assertSee('id="btn-compare"', false);
        $res->assertSee('id="compare-bar"', false);
    }

    public function test_api_requires_authentication(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->getJson('/api/v1/needs/' . $need->id . '/offers');
        $res->assertStatus(401);
    }

    public function test_api_requires_owner_role(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->actingAs($other, 'sanctum')
            ->getJson('/api/v1/needs/' . $need->id . '/offers');
        $res->assertStatus(403);
    }

    public function test_api_returns_offers_for_owner(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $this->makeOffer($need, $provider);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/' . $need->id . '/offers');

        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
    }

    public function test_api_returns_empty_when_no_offers(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/' . $need->id . '/offers');

        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function test_api_includes_provider_relation(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser(['full_name' => 'Abebe Provider']);
        $need = $this->makeNeed($cat, $owner);
        $this->makeOffer($need, $provider);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/' . $need->id . '/offers');

        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('provider', $data[0]);
        $this->assertSame('Abebe Provider', $data[0]['provider']['full_name']);
    }

    public function test_api_returns_404_for_nonexistent_need(): void
    {
        $owner = $this->makeUser();
        $fakeUuid = '00000000-0000-0000-0000-000000000000';

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/' . $fakeUuid . '/offers');

        $res->assertStatus(404);
    }

    public function test_api_returns_offers_ordered_newest_first(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $p1 = $this->makeUser();
        $p2 = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $this->makeOffer($need, $p1, ['proposal_message' => 'First offer submitted yesterday']);
        sleep(1);
        $this->makeOffer($need, $p2, ['proposal_message' => 'Second offer submitted now']);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/' . $need->id . '/offers');

        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(2, $data);
        // Newest first
        $this->assertSame($p2->id, $data[0]['provider_id']);
    }

    public function test_api_returns_all_statuses(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $p1 = $this->makeUser();
        $p2 = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $this->makeOffer($need, $p1, ['status' => Offer::STATUS_PENDING]);
        $this->makeOffer($need, $p2, ['status' => Offer::STATUS_REJECTED]);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/' . $need->id . '/offers');

        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(2, $data);

        $statuses = collect($data)->pluck('status')->all();
        $this->assertContains(Offer::STATUS_PENDING, $statuses);
        $this->assertContains(Offer::STATUS_REJECTED, $statuses);
    }

    public function test_api_does_not_expose_other_needs_offers(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $needA = $this->makeNeed($cat, $owner, ['title' => 'A']);
        $needB = $this->makeNeed($cat, $owner, ['title' => 'B']);

        $this->makeOffer($needA, $provider, ['proposal_message' => 'For need A only']);
        $this->makeOffer($needB, $provider, ['proposal_message' => 'For need B only']);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/' . $needA->id . '/offers');

        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('For need A only', $data[0]['proposal_message']);
    }
}
