<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S008NeedDetailTest extends TestCase
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

    public function test_show_need_page_renders(): void
    {
        $res = $this->get('/needs/abc-123');
        $res->assertStatus(200);
        $res->assertSee('id="content"', false);
    }

    public function test_show_need_page_has_all_states(): void
    {
        $res = $this->get('/needs/abc-123');
        foreach (['state-loading','state-error','state-denied','content'] as $id) {
            $res->assertSee('id="' . $id . '"', false);
        }
    }

    // test_show_need_page_has_ad_slot: removed (ads not in premium design)

    public function test_api_returns_need_by_id(): void
    {
        $cat = $this->makeCategory();
        $u   = $this->makeUser();
        $need = $this->makeNeed($cat, $u);

        $res = $this->getJson('/api/v1/needs/' . $need->id);
        $res->assertStatus(200);
        $res->assertJsonPath('data.title', 'Need a truck to Adama');
        $res->assertJsonPath('data.id', $need->id);
    }

    public function test_api_returns_404_for_nonexistent(): void
    {
        $fakeUuid = '00000000-0000-0000-0000-000000000000';
        $res = $this->getJson('/api/v1/needs/' . $fakeUuid);
        $res->assertStatus(404);
    }

    public function test_api_marks_owner_when_authenticated(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/' . $need->id);

        $res->assertStatus(200);
        $res->assertJsonPath('data.is_owner', true);
    }

    public function test_api_marks_not_owner_for_public_view(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->actingAs($other, 'sanctum')
            ->getJson('/api/v1/needs/' . $need->id);

        $res->assertStatus(200);
        $res->assertJsonPath('data.is_owner', false);
    }

    public function test_api_includes_offer_count(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->getJson('/api/v1/needs/' . $need->id);
        $res->assertStatus(200);
        $res->assertJsonStructure(['data' => ['offer_count']]);
    }

    public function test_cancel_requires_authentication(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->postJson('/api/v1/needs/' . $need->id . '/cancel');
        $res->assertStatus(401);
    }

    public function test_cancel_by_non_owner_returns_403(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->actingAs($other, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/cancel');

        $res->assertStatus(403);
    }

    public function test_cancel_by_owner_succeeds_when_open(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/cancel');

        $res->assertStatus(200);
        $this->assertDatabaseHas('needs', [
            'id' => $need->id,
            'status' => Need::STATUS_CANCELLED,
        ]);
    }

    public function test_cancel_fails_when_not_open(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner, ['status' => Need::STATUS_IN_PROGRESS]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/cancel');

        $res->assertStatus(409);
    }

    public function test_complete_requires_authentication(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner, ['status' => Need::STATUS_IN_PROGRESS]);

        $res = $this->postJson('/api/v1/needs/' . $need->id . '/complete');
        $res->assertStatus(401);
    }

    public function test_complete_by_non_owner_returns_403(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $need = $this->makeNeed($cat, $owner, ['status' => Need::STATUS_IN_PROGRESS]);

        $res = $this->actingAs($other, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/complete');

        $res->assertStatus(403);
    }

    public function test_complete_by_owner_succeeds_when_in_progress(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner, ['status' => Need::STATUS_IN_PROGRESS]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/complete');

        $res->assertStatus(200);
        $this->assertDatabaseHas('needs', [
            'id' => $need->id,
            'status' => Need::STATUS_COMPLETED,
        ]);
    }

    public function test_complete_fails_when_not_in_progress(): void
    {
        $cat = $this->makeCategory();
        $owner = $this->makeUser();
        $need = $this->makeNeed($cat, $owner, ['status' => Need::STATUS_OPEN]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/complete');

        $res->assertStatus(409);
    }
}
