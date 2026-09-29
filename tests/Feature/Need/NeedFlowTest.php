<?php

namespace Tests\Feature\Need;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NeedFlowTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // test DB has no categories — create one manually
        // categories schema: id, slug, name_am, name_en, active, sort_order
        $this->category = Category::create([
            'id'         => (string) Str::uuid(),
            'slug'       => 'electronics',
            'name_am'    => 'ኤሌክትሮኒክስ',
            'name_en'    => 'Electronics',
            'active'     => true,
            'sort_order' => 1,
        ]);
    }

    private function requester(): User
    {
        return User::factory()->create();
    }

    private function validPayload(): array
    {
        return [
            'title'                              => 'Need a laptop for office work',
            'description'                        => 'Looking for a reliable laptop for office documents and everyday tasks.',
            'category_id'                        => $this->category->id,
            'location_text'                      => 'Addis Ababa',
            'budget_min'                         => 500,
            'budget_max'                         => 2000,
            'currency'                           => 'ETB',
            'quantity'                           => 1,
            'deadline_at'                        => now()->addDays(30)->toIso8601String(),
            'offer_deadline_at'                  => now()->addDays(20)->toIso8601String(),
            'telegram_publication_acknowledged'  => true,
        ];
    }

    private function createNeed(User $user, string $status = Need::STATUS_OPEN): Need
    {
        return Need::create([
            'id'           => (string) Str::uuid(),
            'requester_id' => $user->id,
            'category_id'  => $this->category->id,
            'title'        => 'Laptop need',
            'description'  => 'Need a laptop for daily work at the office.',
            'status'       => $status,
            'version'      => 1,
        ]);
    }

    /** Need-01 — create valid need */
    public function test_can_create_need_with_valid_payload(): void
    {
        $user = $this->requester();
        $res = $this->actingAs($user)->postJson('/api/v1/needs', $this->validPayload());

        $res->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('needs', [
            'requester_id' => $user->id,
            'title'        => 'Need a laptop for office work',
            'status'       => Need::STATUS_OPEN,
        ]);
    }

    /** Need-02 — acknowledgement required */
    public function test_create_need_requires_acknowledgement(): void
    {
        $user = $this->requester();
        $payload = $this->validPayload();
        unset($payload['telegram_publication_acknowledged']);

        $res = $this->actingAs($user)->postJson('/api/v1/needs', $payload);

        $res->assertStatus(422);
    }

    /** Need-03 — list public open needs */
    public function test_can_list_public_open_needs(): void
    {
        $user = $this->requester();
        $this->createNeed($user);

        $res = $this->getJson('/api/v1/needs');

        $res->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    /** Need-04 — view own need (is_owner true) */
    public function test_can_view_own_need_with_is_owner_true(): void
    {
        $user = $this->requester();
        $need = $this->createNeed($user);

        $res = $this->actingAs($user)->getJson("/api/v1/needs/{$need->id}");

        $res->assertStatus(200)
            ->assertJsonPath('data.is_owner', true);
    }

    /** Need-05 — cannot update someone else's need */
    public function test_cannot_update_someone_elses_need(): void
    {
        $owner = $this->requester();
        $other = $this->requester();

        $need = $this->createNeed($owner);

        $res = $this->actingAs($other)->putJson("/api/v1/needs/{$need->id}", [
            'title' => 'Attempted unauthorized update',
        ]);

        $res->assertStatus(403);
    }

    /** Need-06 — cancel own open need */
    public function test_can_cancel_own_open_need(): void
    {
        $user = $this->requester();
        $need = $this->createNeed($user);

        $res = $this->actingAs($user)->postJson("/api/v1/needs/{$need->id}/cancel");

        $res->assertStatus(200);
        $this->assertDatabaseHas('needs', [
            'id'     => $need->id,
            'status' => Need::STATUS_CANCELLED,
        ]);
    }
}
