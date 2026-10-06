<?php

namespace Tests\Feature\Message;

use App\Models\Category;
use App\Models\Message;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MessageFlowTest extends TestCase
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

    /** @return array{0: User, 1: User, 2: Offer} */
    private function offerFixture(): array
    {
        $owner = $this->user();
        $provider = $this->user();

        $need = Need::create([
            'id'           => (string) Str::uuid(),
            'requester_id' => $owner->id,
            'category_id'  => $this->category->id,
            'title'        => 'Laptop need',
            'description'  => 'Need a laptop for daily work at the office.',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);

        $offer = Offer::create([
            'id'                => (string) Str::uuid(),
            'need_id'           => $need->id,
            'provider_id'       => $provider->id,
            'offered_price'     => 1500,
            'currency'          => 'ETB',
            'proposal_message'  => 'I can supply a Dell Latitude 7440.',
            'status'            => Offer::STATUS_PENDING,
            'version'           => 1,
        ]);

        return [$owner, $provider, $offer];
    }

    /** Msg-01 — provider can send message on own offer */
    public function test_provider_can_send_message_on_own_offer(): void
    {
        [, $provider, $offer] = $this->offerFixture();

        $res = $this->actingAs($provider)
            ->postJson("/api/v1/offers/{$offer->id}/messages", [
                'content' => 'Hello, when do you need delivery?',
            ]);

        $res->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('messages', [
            'offer_id'  => $offer->id,
            'sender_id' => $provider->id,
        ]);
    }

    /** Msg-02 — requester can send message (participant) */
    public function test_requester_can_send_message(): void
    {
        [$owner, , $offer] = $this->offerFixture();

        $res = $this->actingAs($owner)
            ->postJson("/api/v1/offers/{$offer->id}/messages", [
                'content' => 'Can you deliver by Friday?',
            ]);

        $res->assertStatus(201);
    }

    /** Msg-03 — non-participant cannot send (404 disguise) */
    public function test_non_participant_cannot_send_message(): void
    {
        [, , $offer] = $this->offerFixture();
        $outsider = $this->user();

        $res = $this->actingAs($outsider)
            ->postJson("/api/v1/offers/{$offer->id}/messages", [
                'content' => 'Trying to sneak in.',
            ]);

        $res->assertStatus(404);
    }

    /** Msg-04 — participant can list messages */
    public function test_can_list_messages_for_own_offer(): void
    {
        [, $provider, $offer] = $this->offerFixture();

        $res = $this->actingAs($provider)
            ->getJson("/api/v1/offers/{$offer->id}/messages");

        $res->assertStatus(200)
            ->assertJsonPath('success', true);
    }
    public function test_replayed_send_creates_only_one_message(): void
    {
        [, $provider, $offer] = $this->offerFixture();
        $path = "/api/v1/offers/{$offer->id}/messages";
        $headers = ['Idempotency-Key' => 'message-retry-1'];
        $this->actingAs($provider)->postJson($path, ['content' => 'One message'], $headers)->assertCreated();
        $this->postJson($path, ['content' => 'One message'], $headers)->assertCreated();
        $this->assertSame(1, Message::where('offer_id', $offer->id)->count());
    }
}
