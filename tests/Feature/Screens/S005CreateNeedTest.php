<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S005CreateNeedTest extends TestCase
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

    public function test_create_need_page_renders(): void
    {
        $res = $this->get('/needs/new');
        $res->assertStatus(200);
        $res->assertSee('id="need-form"', false);
    }

    public function test_create_need_page_has_required_fields(): void
    {
        $res = $this->get('/needs/new');
        foreach (['id="title"','id="category_id"','id="description"','id="location_text"',
                  'id="budget_min"','id="budget_max"','id="currency"','id="quantity"',
                  'id="deadline_at"','id="offer_deadline_at"','id="telegram_ack"'] as $marker) {
            $res->assertSee($marker, false);
        }
    }

    public function test_create_need_page_has_submit_and_cancel(): void
    {
        $res = $this->get('/needs/new');
        $res->assertSee('id="submit-btn"', false);
        $res->assertSee('href="/browse"', false);
    }

    public function test_create_need_requires_authentication(): void
    {
        $cat = $this->makeCategory();
        $res = $this->postJson('/api/v1/needs', [
            'title' => 'Need a truck to Adama',
            'description' => 'Looking for a refrigerated truck.',
            'category_id' => $cat->id,
            'telegram_publication_acknowledged' => true,
        ]);
        $res->assertStatus(401);
    }

    public function test_create_need_succeeds_with_valid_payload(): void
    {
        $cat = $this->makeCategory();
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'Need a refrigerated truck to Adama',
            'description' => 'Looking for a refrigerated truck for 10kg of produce.',
            'category_id' => $cat->id,
            'location_text' => 'Addis Ababa',
            'budget_min' => 5000,
            'budget_max' => 8000,
            'currency' => 'ETB',
            'quantity' => 10,
            'telegram_publication_acknowledged' => true,
        ]);

        $res->assertStatus(201);
        $this->assertDatabaseHas('needs', [
            'title' => 'Need a refrigerated truck to Adama',
            'requester_id' => $user->id,
        ]);
    }

    public function test_create_need_requires_title(): void
    {
        $cat = $this->makeCategory();
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'description' => 'Looking for a refrigerated truck for 10kg of produce.',
            'category_id' => $cat->id,
            'telegram_publication_acknowledged' => true,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['title']);
    }

    public function test_create_need_requires_description(): void
    {
        $cat = $this->makeCategory();
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'Need a refrigerated truck',
            'category_id' => $cat->id,
            'telegram_publication_acknowledged' => true,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['description']);
    }

    public function test_create_need_requires_category(): void
    {
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'Need a refrigerated truck',
            'description' => 'Looking for a refrigerated truck for 10kg of produce.',
            'telegram_publication_acknowledged' => true,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['category_id']);
    }

    public function test_create_need_requires_telegram_acknowledgement(): void
    {
        $cat = $this->makeCategory();
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'Need a refrigerated truck',
            'description' => 'Looking for a refrigerated truck for 10kg of produce.',
            'category_id' => $cat->id,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['telegram_publication_acknowledged']);
    }

    public function test_create_need_rejects_title_below_min(): void
    {
        $cat = $this->makeCategory();
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'abc',
            'description' => 'Looking for a refrigerated truck for 10kg of produce.',
            'category_id' => $cat->id,
            'telegram_publication_acknowledged' => true,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['title']);
    }

    public function test_create_need_rejects_description_below_min(): void
    {
        $cat = $this->makeCategory();
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'Need a refrigerated truck',
            'description' => 'Short desc',
            'category_id' => $cat->id,
            'telegram_publication_acknowledged' => true,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['description']);
    }

    public function test_create_need_rejects_invalid_category(): void
    {
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'Need a refrigerated truck',
            'description' => 'Looking for a refrigerated truck for 10kg of produce.',
            'category_id' => '00000000-0000-0000-0000-000000000000',
            'telegram_publication_acknowledged' => true,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['category_id']);
    }

    public function test_create_need_rejects_budget_max_below_min(): void
    {
        $cat = $this->makeCategory();
        $user = $this->makeUser();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'Need a refrigerated truck',
            'description' => 'Looking for a refrigerated truck for 10kg of produce.',
            'category_id' => $cat->id,
            'budget_min' => 10000,
            'budget_max' => 5000,
            'telegram_publication_acknowledged' => true,
        ]);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['budget_max']);
    }

    public function test_new_need_starts_in_open_status(): void
    {
        $cat = $this->makeCategory();
        $user = $this->makeUser();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/needs', [
            'title' => 'Need a refrigerated truck to Adama',
            'description' => 'Looking for a refrigerated truck for 10kg of produce.',
            'category_id' => $cat->id,
            'telegram_publication_acknowledged' => true,
        ]);

        $need = Need::where('requester_id', $user->id)->first();
        $this->assertNotNull($need);
        $this->assertSame(Need::STATUS_OPEN, $need->status);
    }
}
