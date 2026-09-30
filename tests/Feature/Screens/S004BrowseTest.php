<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S004BrowseTest extends TestCase
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

    public function test_browse_page_renders(): void
    {
        $res = $this->get('/browse');
        $res->assertStatus(200);
        $res->assertSee('id="keyword"', false);
        $res->assertSee('maxlength="255"', false);
        $res->assertSee('id="chips"', false);
    }

    public function test_browse_page_has_all_states(): void
    {
        $res = $this->get('/browse');
        foreach (['id="feed"','id="empty"','id="error"'] as $marker) {
            $res->assertSee($marker, false);
        }
    }

    public function test_browse_page_has_create_fab_and_nav(): void
    {
        $res = $this->get('/browse');
        $res->assertSee('class="fab"', false);
        foreach (['/browse','/my/needs','/my/offers','/notifications','/profile'] as $href) {
            $res->assertSee('href="' . $href . '"', false);
        }
    }

    public function test_browse_page_has_both_ad_slots(): void
    {
        $res = $this->get('/browse');
        $res->assertSee('AD_BROWSE_INLINE_01', false);
        $res->assertSee('AD_SEARCH_RESULTS_INLINE_01', false);
    }

    public function test_categories_endpoint_returns_active_only(): void
    {
        $this->makeCategory(['name_en' => 'Transport', 'slug' => 'transport']);
        $this->makeCategory(['name_en' => 'Inactive', 'slug' => 'inactive', 'active' => false]);

        $res = $this->getJson('/api/v1/categories');
        $res->assertStatus(200);
        $slugs = collect($res->json('data'))->pluck('slug')->all();
        $this->assertContains('transport', $slugs);
        $this->assertNotContains('inactive', $slugs);
    }

    public function test_needs_endpoint_returns_only_open(): void
    {
        $cat = $this->makeCategory();
        $u   = $this->makeUser();
        $this->makeNeed($cat, $u, ['title' => 'OPEN visible']);
        $this->makeNeed($cat, $u, ['title' => 'CANCELLED hidden', 'status' => Need::STATUS_CANCELLED]);

        $res = $this->getJson('/api/v1/needs');
        $res->assertStatus(200);
        $titles = collect($res->json('data'))->pluck('title')->all();
        $this->assertContains('OPEN visible', $titles);
        $this->assertNotContains('CANCELLED hidden', $titles);
    }

    public function test_needs_endpoint_has_meta_pagination(): void
    {
        $cat = $this->makeCategory();
        $u   = $this->makeUser();
        for ($i = 0; $i < 3; $i++) { $this->makeNeed($cat, $u); }

        $res = $this->getJson('/api/v1/needs?per_page=2');
        $res->assertStatus(200);
        $res->assertJsonStructure(['data', 'meta' => ['page','per_page','total','has_more']]);
    }

    public function test_needs_endpoint_filters_by_keyword(): void
    {
        $cat = $this->makeCategory();
        $u   = $this->makeUser();
        $this->makeNeed($cat, $u, ['title' => 'Refrigerated truck', 'description' => 'Cold chain logistics.']);
        $this->makeNeed($cat, $u, ['title' => 'Graphic designer', 'description' => 'Logo design work.']);

        $res = $this->getJson('/api/v1/needs?keyword=refrigerated');
        $res->assertStatus(200);
        $titles = collect($res->json('data'))->pluck('title')->all();
        $this->assertContains('Refrigerated truck', $titles);
        $this->assertNotContains('Graphic designer', $titles);
    }

    public function test_needs_endpoint_filters_by_category(): void
    {
        $catA = $this->makeCategory(['slug' => 'cat-a', 'name_en' => 'A']);
        $catB = $this->makeCategory(['slug' => 'cat-b', 'name_en' => 'B']);
        $u    = $this->makeUser();
        $this->makeNeed($catA, $u, ['title' => 'Need in A']);
        $this->makeNeed($catB, $u, ['title' => 'Need in B']);

        $res = $this->getJson('/api/v1/needs?category_id=' . $catA->id);
        $res->assertStatus(200);
        $titles = collect($res->json('data'))->pluck('title')->all();
        $this->assertContains('Need in A', $titles);
        $this->assertNotContains('Need in B', $titles);
    }

    public function test_needs_endpoint_sorts_budget_low(): void
    {
        $cat = $this->makeCategory();
        $u   = $this->makeUser();
        $this->makeNeed($cat, $u, ['title' => 'High', 'budget_min' => 10000]);
        $this->makeNeed($cat, $u, ['title' => 'Low',  'budget_min' => 1000]);

        $res = $this->getJson('/api/v1/needs?sort=budget_low');
        $res->assertStatus(200);
        $titles = collect($res->json('data'))->pluck('title')->all();
        $this->assertSame('Low', $titles[0]);
    }

    public function test_rejects_keyword_over_255(): void
    {
        $res = $this->getJson('/api/v1/needs?keyword=' . str_repeat('a', 256));
        $res->assertStatus(422);
    }

    public function test_rejects_invalid_sort(): void
    {
        $res = $this->getJson('/api/v1/needs?sort=drop_table');
        $res->assertStatus(422);
    }

    public function test_rejects_per_page_over_50(): void
    {
        $res = $this->getJson('/api/v1/needs?per_page=999');
        $res->assertStatus(422);
    }

    public function test_rejects_invalid_category_uuid(): void
    {
        $res = $this->getJson('/api/v1/needs?category_id=not-a-uuid');
        $res->assertStatus(422);
    }

    public function test_rejects_nonexistent_category_id(): void
    {
        $fakeUuid = '00000000-0000-0000-0000-000000000000';
        $res = $this->getJson('/api/v1/needs?category_id=' . $fakeUuid);
        $res->assertStatus(422);
    }
}
