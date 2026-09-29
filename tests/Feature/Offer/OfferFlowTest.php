<?php

namespace Tests\Feature\Offer;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OfferFlowTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::create([
            'id'         => (string) Str::uuid(),
            'slug'       => 'electronics',
            'name_am'    => 'ኤሌክትሮኒክስ',
            'name_en'    => 'Electronics',
            'active'     => true,
            'sort_order' => 1,
        ]);
    }

    private function user(): User
    {
        return User::factory()->create();
    }

    private function openNeed(User $owner): Need
    {
        return Need::create([
            'id'           => (string) Str::uuid(),
            'requester_id' => $owner->id,
            'category_id'  => $this->category->id,
            'title'        => 'Laptop need',
            'description'  => 'Need a laptop for daily work at the office.',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);
    }

    private function offerPayload(): array
    {
        return [
            'offered_price'     => 1500,
            'currency'          => 'ETB',
            'proposal_message'  => 'I can supply a Dell Latitude 7440 with 16GB RAM.',
            'delivery_time_text'=> '3 business days',
        ];
    }

    /** Offer-01 — provider submits offer (201) */
    public function test_provider_can_submit_offer(): void
    {
        $owner = $this->user();
        $provider = $this->user();
        $need = $this->openNeed($owner);

        $res = $this->actingAs($provider)
            ->postJson("/api/v1/needs/{$need->id}/offers", $this->offerPayload());

        $res->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('offers', [
            'need_id'     => $need->id,
            'provider_id' => $provider->id,
            'status'      => Offer::STATUS_PENDING,
        ]);
    }

    /** Offer-02 — cannot offer on own need (403) */
    public function test_cannot_offer_on_own_need(): void
    {
        $owner = $this->user();
        $need = $this->openNeed($owner);

        $res = $this->actingAs($owner)
            ->postJson("/api/v1/needs/{$need->id}/offers", $this->offerPayload());

        $res->assertStatus(403);
    }

    /** Offer-03 — duplicate offer blocked (409) */
    public function test_cannot_submit_duplicate_offer(): void
    {
        $owner = $this->user();
        $provider = $this->user();
        $need = $this->openNeed($owner);

        $this->actingAs($provider)
            ->postJson("/api/v1/needs/{$need->id}/offers", $this->offerPayload())
            ->assertStatus(201);

        $res = $this->actingAs($provider)
            ->postJson("/api/v1/needs/{$need->id}/offers", $this->offerPayload());

        $res->assertStatus(409);
    }

    /** Offer-04 — owner accepts offer (need → IN_PROGRESS) */
    public function test_owner_can_accept_offer(): void
    {
        $owner = $this->user();
        $provider = $this->user();
        $need = $this->openNeed($owner);

        $offerId = $this->actingAs($provider)
            ->postJson("/api/v1/needs/{$need->id}/offers", $this->offerPayload())
            ->json('data.id');

        $res = $this->actingAs($owner)
            ->postJson("/api/v1/offers/{$offerId}/accept");

        $res->assertStatus(200);

        $this->assertDatabaseHas('offers', [
            'id'     => $offerId,
            'status' => Offer::STATUS_ACCEPTED,
        ]);
        $this->assertDatabaseHas('needs', [
            'id'     => $need->id,
            'status' => Need::STATUS_IN_PROGRESS,
        ]);
    }

    /** Offer-05 — non-owner cannot accept (403) */
    public function test_non_owner_cannot_accept_offer(): void
    {
        $owner = $this->user();
        $provider = $this->user();
        $outsider = $this->user();
        $need = $this->openNeed($owner);

        $offerId = $this->actingAs($provider)
            ->postJson("/api/v1/needs/{$need->id}/offers", $this->offerPayload())
            ->json('data.id');

        $res = $this->actingAs($outsider)
            ->postJson("/api/v1/offers/{$offerId}/accept");

        $res->assertStatus(403);
    }

    /** Offer-06 — provider withdraws own pending offer */
    public function test_provider_can_withdraw_own_offer(): void
    {
        $owner = $this->user();
        $provider = $this->user();
        $need = $this->openNeed($owner);

        $offerId = $this->actingAs($provider)
            ->postJson("/api/v1/needs/{$need->id}/offers", $this->offerPayload())
            ->json('data.id');

        $res = $this->actingAs($provider)
            ->postJson("/api/v1/offers/{$offerId}/withdraw");

        $res->assertStatus(200);
        $this->assertDatabaseHas('offers', [
            'id'     => $offerId,
            'status' => Offer::STATUS_WITHDRAWN,
        ]);
    }
}
