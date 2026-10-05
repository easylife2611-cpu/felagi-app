<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S009MyNeedsTest extends TestCase
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

    public function test_my_needs_page_renders(): void
    {
        $res = $this->get('/my/needs');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
        $res->assertSee('id="state-content"', false);
    }

    public function test_my_needs_page_has_all_states(): void
    {
        $res = $this->get('/my/needs');
        foreach (['state-loading','state-content','state-empty','state-error'] as $id) {
            $res->assertSee('id="' . $id . '"', false);
        }
    }

    public function test_my_needs_page_has_status_filters(): void
    {
        $res = $this->get('/my/needs');
        foreach (['OPEN','IN_PROGRESS','COMPLETED','CANCELLED'] as $s) {
            $res->assertSee('data-status="' . $s . '"', false);
        }
    }

    public function test_my_needs_page_has_create_fab_and_nav(): void
    {
        $res = $this->get('/my/needs');
        $res->assertSee('felagi-fab', false);
        $res->assertSee('href="/needs/new"', false);
        foreach (['/browse','/my/offers','/profile'] as $href) {
            $res->assertSee('href="' . $href . '"', false);
        }
    }

    public function test_my_needs_requires_authentication(): void
    {
        $res = $this->getJson('/api/v1/my/needs');
        $res->assertStatus(401);
    }

    public function test_my_needs_returns_only_own_needs(): void
    {
        $cat = $this->makeCategory();
        $me = $this->makeUser();
        $other = $this->makeUser();

        $this->makeNeed($cat, $me, ['title' => 'Mine']);
        $this->makeNeed($cat, $other, ['title' => 'Other']);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/needs');
        $res->assertStatus(200);
        $titles = collect($res->json('data'))->pluck('title')->all();
        $this->assertContains('Mine', $titles);
        $this->assertNotContains('Other', $titles);
    }

    public function test_my_needs_filters_by_status(): void
    {
        $cat = $this->makeCategory();
        $me = $this->makeUser();

        $this->makeNeed($cat, $me, ['title' => 'Open one', 'status' => Need::STATUS_OPEN]);
        $this->makeNeed($cat, $me, ['title' => 'Cancelled one', 'status' => Need::STATUS_CANCELLED]);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/needs?status=OPEN');
        $res->assertStatus(200);
        $titles = collect($res->json('data'))->pluck('title')->all();
        $this->assertContains('Open one', $titles);
        $this->assertNotContains('Cancelled one', $titles);
    }

    public function test_my_needs_includes_all_statuses_when_no_filter(): void
    {
        $cat = $this->makeCategory();
        $me = $this->makeUser();

        $this->makeNeed($cat, $me, ['title' => 'Open one', 'status' => Need::STATUS_OPEN]);
        $this->makeNeed($cat, $me, ['title' => 'Cancelled one', 'status' => Need::STATUS_CANCELLED]);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/needs');
        $res->assertStatus(200);
        $titles = collect($res->json('data'))->pluck('title')->all();
        $this->assertContains('Open one', $titles);
        $this->assertContains('Cancelled one', $titles);
    }

    public function test_my_needs_returns_empty_when_user_has_none(): void
    {
        $cat = $this->makeCategory();
        $me = $this->makeUser();
        $other = $this->makeUser();
        $this->makeNeed($cat, $other);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/needs');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function test_my_needs_includes_category(): void
    {
        $cat = $this->makeCategory();
        $me = $this->makeUser();
        $this->makeNeed($cat, $me);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/needs');
        $res->assertStatus(200);
        $res->assertJsonStructure([
            'data' => [
                ['id', 'title', 'status', 'category' => ['id','slug','name_en','name_am']]
            ]
        ]);
    }

    public function test_my_needs_pagination(): void
    {
        $cat = $this->makeCategory();
        $me = $this->makeUser();
        for ($i = 0; $i < 5; $i++) {
            $this->makeNeed($cat, $me, ['title' => 'Need ' . $i]);
        }

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/needs?per_page=2');
        $res->assertStatus(200);
        $res->assertJsonStructure(['data', 'meta' => ['page','per_page','total']]);
        $this->assertSame(2, $res->json('meta.per_page'));
        $this->assertSame(5, $res->json('meta.total'));
    }

    public function test_my_needs_orders_newest_first(): void
    {
        $cat = $this->makeCategory();
        $me = $this->makeUser();

        $old = $this->makeNeed($cat, $me, ['title' => 'Old']);
        sleep(1);
        $new = $this->makeNeed($cat, $me, ['title' => 'New']);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/my/needs');
        $res->assertStatus(200);
        $titles = collect($res->json('data'))->pluck('title')->all();
        $this->assertSame('New', $titles[0]);
    }

    public function test_my_needs_isolated_between_users(): void
    {
        $cat = $this->makeCategory();
        $userA = $this->makeUser();
        $userB = $this->makeUser();

        $this->makeNeed($cat, $userA, ['title' => 'A needs']);
        $this->makeNeed($cat, $userB, ['title' => 'B needs']);

        $resA = $this->actingAs($userA, 'sanctum')->getJson('/api/v1/my/needs');
        $resB = $this->actingAs($userB, 'sanctum')->getJson('/api/v1/my/needs');

        $titlesA = collect($resA->json('data'))->pluck('title')->all();
        $titlesB = collect($resB->json('data'))->pluck('title')->all();

        $this->assertContains('A needs', $titlesA);
        $this->assertNotContains('B needs', $titlesA);
        $this->assertContains('B needs', $titlesB);
        $this->assertNotContains('A needs', $titlesB);
    }
}
