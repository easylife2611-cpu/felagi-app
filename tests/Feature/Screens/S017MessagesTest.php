<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Message;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S017MessagesTest extends TestCase
{
    use RefreshDatabase;

    private function makeOffer(User $owner, User $provider): Offer
    {
        $cat = Category::factory()->create(['name_en'=>'L','name_am'=>'ሎ','slug'=>'cat-'.uniqid(),'active'=>true]);
        $need = Need::factory()->create([
            'category_id' => $cat->id, 'requester_id' => $owner->id,
            'title' => 'Need', 'description' => 'Ref truck.', 'status' => Need::STATUS_OPEN,
        ]);
        return Offer::factory()->create([
            'need_id' => $need->id, 'provider_id' => $provider->id,
            'status' => Offer::STATUS_PENDING,
        ]);
    }

    public function test_page_renders(): void
    {
        $res = $this->get('/offers/abc/messages');
        $res->assertStatus(200);
        $res->assertSee('id="composer"', false);
    }

    public function test_api_requires_auth(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $offer = $this->makeOffer($owner, $provider);

        $this->getJson('/api/v1/offers/'.$offer->id.'/messages')->assertStatus(401);
    }

    public function test_provider_can_list_messages(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $offer = $this->makeOffer($owner, $provider);

        Message::factory()->create([
            'offer_id' => $offer->id,
            'sender_id' => $provider->id,
            'content' => 'Hello, I can deliver.',
        ]);

        $res = $this->actingAs($provider, 'sanctum')->getJson('/api/v1/offers/'.$offer->id.'/messages');
        $res->assertStatus(200);
        $this->assertCount(1, $res->json('data'));
    }

    public function test_owner_can_list_messages(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $offer = $this->makeOffer($owner, $provider);

        $res = $this->actingAs($owner, 'sanctum')->getJson('/api/v1/offers/'.$offer->id.'/messages');
        $res->assertStatus(200);
    }

    public function test_third_party_cannot_list(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $other = User::factory()->create();
        $offer = $this->makeOffer($owner, $provider);

        $res = $this->actingAs($other, 'sanctum')->getJson('/api/v1/offers/'.$offer->id.'/messages');
        $res->assertStatus(404);
    }

    public function test_provider_can_send_message(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $offer = $this->makeOffer($owner, $provider);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offers/'.$offer->id.'/messages', ['content' => 'Hi there!']);

        $res->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'offer_id' => $offer->id,
            'sender_id' => $provider->id,
        ]);
    }

    public function test_message_content_required(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $offer = $this->makeOffer($owner, $provider);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offers/'.$offer->id.'/messages', ['content' => '']);

        $res->assertStatus(422);
    }
}
