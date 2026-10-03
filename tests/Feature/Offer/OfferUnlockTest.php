<?php

declare(strict_types=1);

namespace Tests\Feature\Offer;

use App\Models\Category;
use App\Models\Need;
use App\Models\OfferSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * L347-G — S023 Offer Unlock API tests (updated for new schema).
 */
final class OfferUnlockTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        config(['payments.unlock' => [
            'feature_enabled' => false,
            'amount_minor'    => 0,
            'policy_version'  => '1.3',
        ]]);
    }

    private function makeNeed(User $owner): Need
    {
        $cat = Category::create([
            'slug' => 'ul-' . Str::lower(Str::random(6)),
            'name_am' => 'ምድብ', 'name_en' => 'Cat',
            'active' => true, 'sort_order' => 1,
        ]);
        return Need::create([
            'requester_id' => $owner->id,
            'category_id'  => $cat->id,
            'title'        => 'Unlock test need',
            'description'  => 'Desc',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);
    }

    private function payload(string $needId, ?string $idem = null, ?string $hash = null): array
    {
        return [
            'need_id'            => $needId,
            'idempotency_key'    => $idem ?? 'idem-' . Str::random(20),
            'draft_id'           => (string) Str::uuid(),
            'draft_version'      => 1,
            'draft_hash'         => $hash ?? hash('sha256', 'd-' . Str::random(8)),
            'offered_price'      => '500.00',
            'currency'           => 'ETB',
            'proposal_message'   => 'I can deliver this well enough.',
            'delivery_time_text' => '3 days',
            'availability_text'  => 'Now',
        ];
    }

    public function test_requires_auth(): void
    {
        $fakeId = (string) Str::uuid();
        $res = $this->postJson('/api/v1/offer-submissions', ['need_id' => $fakeId]);
        $this->assertContains($res->status(), [401, 403, 302]);
    }

    public function test_provider_creates_submission_free_path(): void
    {
        $owner    = User::factory()->create();
        $provider = User::factory()->create();
        $need     = $this->makeNeed($owner);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', $this->payload($need->id));

        $res->assertStatus(201);
        $this->assertDatabaseHas('offer_submissions', [
            'need_id'     => $need->id,
            'provider_id' => $provider->id,
            'state'       => OfferSubmission::STATE_SUBMITTED,
        ]);
        $this->assertDatabaseHas('offers', [
            'need_id'     => $need->id,
            'provider_id' => $provider->id,
        ]);
    }

    public function test_owner_cannot_unlock_own_need(): void
    {
        $owner = User::factory()->create();
        $need  = $this->makeNeed($owner);

        $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/offer-submissions', $this->payload($need->id))
            ->assertStatus(403);
    }

    public function test_idempotent_same_payload_returns_existing(): void
    {
        $owner    = User::factory()->create();
        $provider = User::factory()->create();
        $need     = $this->makeNeed($owner);

        $idem = 'idem-fixed-' . Str::random(10);
        $hash = hash('sha256', 'fixed-draft');

        $r1 = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', $this->payload($need->id, $idem, $hash));
        $r1->assertStatus(201);

        $r2 = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', $this->payload($need->id, $idem, $hash));
        $r2->assertStatus(201);

        $this->assertSame(
            $r1->json('data.id'),
            $r2->json('data.id'),
            'Idempotent call must return same submission'
        );
        $this->assertSame(1, OfferSubmission::count());
    }

    public function test_idempotency_conflict_returns_409(): void
    {
        $owner    = User::factory()->create();
        $provider = User::factory()->create();
        $need     = $this->makeNeed($owner);

        $idem = 'idem-conflict-' . Str::random(8);

        $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions',
                $this->payload($need->id, $idem, hash('sha256', 'a')))
            ->assertStatus(201);

        $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions',
                $this->payload($need->id, $idem, hash('sha256', 'b')))
            ->assertStatus(409);
    }

    public function test_show_requires_owner(): void
    {
        $owner    = User::factory()->create();
        $provider = User::factory()->create();
        $other    = User::factory()->create();
        $need     = $this->makeNeed($owner);

        $res = $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', $this->payload($need->id));
        $res->assertStatus(201);
        $id = $res->json('data.id');

        $this->actingAs($other, 'sanctum')
            ->getJson('/api/v1/offer-submissions/' . $id)
            ->assertStatus(403);

        $this->actingAs($provider, 'sanctum')
            ->getJson('/api/v1/offer-submissions/' . $id)
            ->assertStatus(200);
    }

    public function test_validation_requires_idempotency_key(): void
    {
        $owner    = User::factory()->create();
        $provider = User::factory()->create();
        $need     = $this->makeNeed($owner);

        $payload = $this->payload($need->id);
        unset($payload['idempotency_key']);

        $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['idempotency_key']);
    }

    public function test_validation_requires_draft_id(): void
    {
        $owner    = User::factory()->create();
        $provider = User::factory()->create();
        $need     = $this->makeNeed($owner);

        $payload = $this->payload($need->id);
        unset($payload['draft_id']);

        $this->actingAs($provider, 'sanctum')
            ->postJson('/api/v1/offer-submissions', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['draft_id']);
    }
}
