<?php

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S020RatingTest extends TestCase
{
    use RefreshDatabase;

    private function makeNeed(User $owner, string $status = Need::STATUS_COMPLETED): Need
    {
        $cat = Category::factory()->create(['name_en'=>'L','name_am'=>'ሎ','slug'=>'cat-'.uniqid(),'active'=>true]);
        return Need::factory()->create([
            'category_id' => $cat->id, 'requester_id' => $owner->id,
            'title' => 'Need', 'description' => 'Ref.', 'status' => $status,
        ]);
    }

    public function test_page_renders(): void
    {
        $res = $this->get('/needs/abc-123/rating');
        $res->assertStatus(200);
        $res->assertSee('id="stars"', false);
        $res->assertSee('id="rate-form"', false);
    }

    public function test_page_has_five_stars(): void
    {
        $res = $this->get('/needs/abc-123/rating');
        $res->assertSee('data-value="1"', false);
        $res->assertSee('data-value="5"', false);
        $res->assertSee('id="submit-btn"', false);
    }

    public function test_rating_requires_auth(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $res = $this->postJson('/api/v1/needs/'.$need->id.'/ratings', [
            'to_user_id' => $owner->id, 'score' => 5,
        ]);
        $res->assertStatus(401);
    }

    public function test_rating_requires_completed_need(): void
    {
        $owner = User::factory()->create();
        $provider = User::factory()->create();
        $need = $this->makeNeed($owner, Need::STATUS_OPEN);
        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/ratings', [
                'to_user_id' => $provider->id, 'score' => 5,
            ]);
        $res->assertStatus(422);
    }

    public function test_page_has_all_five_star_values(): void
    {
        $res = $this->get('/needs/abc-123/rating');
        foreach (['1', '2', '3', '4', '5'] as $v) {
            $res->assertSee('data-value="'.$v.'"', false);
        }
    }

    public function test_page_has_stars_and_form_containers(): void
    {
        $res = $this->get('/needs/abc-123/rating');
        $res->assertSee('id="stars"', false);
        $res->assertSee('id="rate-form"', false);
        $res->assertSee('id="submit-btn"', false);
    }

    public function test_rating_rejects_unknown_need(): void
    {
        $user = User::factory()->create();
        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/needs/00000000-0000-0000-0000-000000000000/ratings', [
                'to_user_id' => $user->id, 'score' => 5,
            ]);
        $res->assertStatus(404);
    }

    public function test_rating_route_is_registered(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        // The store() method runs first, so any POST to the route
        // proves the route exists. 401 (no auth) is the expected response.
        $res = $this->postJson('/api/v1/needs/'.$need->id.'/ratings', [
            'to_user_id' => $owner->id, 'score' => 5,
        ]);
        $res->assertStatus(401);
    }
}
