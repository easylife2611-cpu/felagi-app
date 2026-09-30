<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S012OfferDetailTest extends TestCase
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

    public function test_offer_detail_page_renders(): void
    {
        $res = $this->get('/offers/abc-123');
        $res->assertStatus(200);
        $res->assertSee('id="content"', false);
    }

    public function test_offer_detail_page_has_all_states(): void
    {
        $res = $this->get('/offers/abc-123');
        foreach (['state-loading','state-denied','state-error','content'] as $id) {
            $res->assertSee('id="' . $id . '"', false);
        }
    }

    public function test_offer_detail_page_has_three_action_buttons(): void
    {
        $res = $this->get('/offers/abc-123');
        $res->assertSee('id="btn-accept"', false);
        $res->assertSee('id="btn-reject"', false);
        $res->assertSee('id="btn-withdraw"', false);
    }

    public function test_api_requires_authentication(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->getJson('/api/v1/offers/' . $offer->id);
        $res->assertStatus(401);
    }

    public function test_api_provider_can_view_own_offer(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($provider, 'sanctum')
            ->getJson('/api/v1/offers/' . $offer->id);

        $res->assertStatus(200);
        $res->assertJsonPath('data.id', $offer->id);
    }

    public function test_api_owner_can_view_received_offer(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/offers/' . $offer->id);

        $res->assertStatus(200);
        $res->assertJsonPath('data.id', $offer->id);
    }

    public function test_api_returns_404_for_third_party(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $other = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($other, 'sanctum')
            ->getJson('/api/v1/offers/' . $offer->id);

        $res->assertStatus(404);
    }

    public function test_accept_requires_owner(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offers/' . $offer->id . '/accept');

        $res->assertStatus(403);
    }

    public function test_accept_changes_status_to_accepted(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/offers/' . $offer->id . '/accept');

        $res->assertStatus(200);
        $offer->refresh();
        $this->assertSame(Offer::STATUS_ACCEPTED, $offer->status);
    }

    public function test_reject_requires_owner(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offers/' . $offer->id . '/reject');

        $res->assertStatus(403);
    }

    public function test_reject_changes_status_to_rejected(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/offers/' . $offer->id . '/reject');

        $res->assertStatus(200);
        $offer->refresh();
        $this->assertSame(Offer::STATUS_REJECTED, $offer->status);
    }

    public function test_withdraw_requires_provider(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/offers/' . $offer->id . '/withdraw');

        $res->assertStatus(403);
    }

    public function test_withdraw_changes_status_to_withdrawn(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offers/' . $offer->id . '/withdraw');

        $res->assertStatus(200);
        $offer->refresh();
        $this->assertSame(Offer::STATUS_WITHDRAWN, $offer->status);
    }

    public function test_cannot_accept_already_terminal_offer(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider, ['status' => Offer::STATUS_REJECTED]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/offers/' . $offer->id . '/accept');

        $res->assertStatus(409);
    }

    public function test_cannot_withdraw_already_terminal_offer(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);
        $offer = $this->makeOffer($need, $provider, ['status' => Offer::STATUS_ACCEPTED]);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offers/' . $offer->id . '/withdraw');

        $res->assertStatus(409);
    }

    public function test_offer_not_found_returns_404(): void
    {
        $owner = $this->makeUser();
        $fakeUuid = '00000000-0000-0000-0000-000000000000';

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/offers/' . $fakeUuid);

        $res->assertStatus(404);
    }
}
