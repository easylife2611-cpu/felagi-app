<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S011SubmitOfferTest extends TestCase
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

    private function validPayload(): array
    {
        return [
            'offered_price'      => 7500,
            'currency'           => 'ETB',
            'proposal_message'   => 'I can provide a refrigerated truck within 3 business days.',
            'delivery_time_text' => '3 business days',
            'availability_text'  => 'Available from Monday',
            'additional_notes'   => 'Includes packing materials.',
        ];
    }

    public function test_submit_offer_page_renders(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->get('/needs/' . $need->id . '/offers/new');
        $res->assertStatus(200);
        $res->assertSee('id="offer-form"', false);
    }

    public function test_submit_offer_page_has_required_fields(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->get('/needs/' . $need->id . '/offers/new');

        foreach ([
            'id="offered_price"',
            'id="currency"',
            'id="proposal_message"',
            'id="delivery_time_text"',
            'id="availability_text"',
            'id="additional_notes"',
            'id="submit-btn"',
        ] as $marker) {
            $res->assertSee($marker, false);
        }
    }

    public function test_submit_offer_page_has_all_states(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->get('/needs/' . $need->id . '/offers/new');
        foreach (['state-loading','state-need-error','state-form'] as $id) {
            $res->assertSee('id="' . $id . '"', false);
        }
    }

    public function test_submit_offer_requires_authentication(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->postJson('/api/v1/needs/' . $need->id . '/offers', $this->validPayload());
        $res->assertStatus(401);
    }

    public function test_submit_offer_succeeds_with_valid_payload(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $this->validPayload());

        $res->assertStatus(201);
        $this->assertDatabaseHas('offers', [
            'need_id'     => $need->id,
            'provider_id' => $provider->id,
            'status'      => Offer::STATUS_PENDING,
        ]);
    }

    public function test_submit_offer_requires_offered_price(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $payload = $this->validPayload();
        unset($payload['offered_price']);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $payload);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['offered_price']);
    }

    public function test_submit_offer_requires_proposal_message(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $payload = $this->validPayload();
        unset($payload['proposal_message']);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $payload);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['proposal_message']);
    }

    public function test_submit_offer_rejects_short_proposal_message(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $payload = $this->validPayload();
        $payload['proposal_message'] = 'Too short';

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $payload);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['proposal_message']);
    }

    public function test_submit_offer_rejects_negative_price(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $payload = $this->validPayload();
        $payload['offered_price'] = -100;

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $payload);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['offered_price']);
    }

    public function test_owner_cannot_offer_on_own_need(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $this->validPayload());

        $res->assertStatus(403);
    }

    public function test_cannot_offer_on_non_open_need(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner, ['status' => Need::STATUS_COMPLETED]);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $this->validPayload());

        $res->assertStatus(409);
    }

    public function test_cannot_offer_after_deadline_passed(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner, [
            'offer_deadline_at' => now()->subDay(),
        ]);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $this->validPayload());

        $res->assertStatus(422);
    }

    public function test_cannot_submit_duplicate_offer(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        Offer::factory()->create([
            'need_id'     => $need->id,
            'provider_id' => $provider->id,
            'status'      => Offer::STATUS_PENDING,
        ]);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $this->validPayload());

        $res->assertStatus(409);
    }

    public function test_new_offer_starts_in_pending_status(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $provider = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/offers', $this->validPayload());

        $offer = Offer::where('need_id', $need->id)
            ->where('provider_id', $provider->id)
            ->first();

        $this->assertNotNull($offer);
        $this->assertSame(Offer::STATUS_PENDING, $offer->status);
    }

    public function test_offer_404_for_nonexistent_need(): void
    {
        $provider = $this->makeUser();
        $fakeUuid = '00000000-0000-0000-0000-000000000000';

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/needs/' . $fakeUuid . '/offers', $this->validPayload());

        $res->assertStatus(404);
    }
}
