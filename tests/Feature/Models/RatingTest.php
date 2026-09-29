<?php

namespace Tests\Feature\Models;

use App\Models\Need;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Illuminate\Support\Str;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makeRating(array $overrides = []): Rating
    {
        $requester = User::factory()->create();
        $provider = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id' => $this->makeCategory(),
            'title' => 'Test Need',
            'description' => 'Test Description',
            'status' => 'OPEN',
        ]);

        return Rating::create(array_merge([
            'need_id' => $need->id,
            'from_user_id' => $requester->id,
            'to_user_id' => $provider->id,
            'score' => 5,
            'review' => 'Great service',
            'created_at' => now(),
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $r = $this->makeRating();
        $this->assertNotNull($r->id);
        $this->assertTrue(Str::isUuid($r->id));
    }

    public function test_uses_no_timestamps(): void
    {
        $r = new Rating();
        $this->assertFalse($r->usesTimestamps());
    }

    public function test_score_casts_to_integer(): void
    {
        $r = $this->makeRating(['score' => 4]);
        $r->refresh();
        $this->assertIsInt($r->score);
        $this->assertSame(4, $r->score);
    }

    public function test_created_at_casts_to_datetime(): void
    {
        $r = $this->makeRating();
        $r->refresh();
        $this->assertInstanceOf(\Carbon\Carbon::class, $r->created_at);
    }

    public function test_need_relationship(): void
    {
        $r = $this->makeRating();
        $this->assertInstanceOf(Need::class, $r->need);
        $this->assertSame($r->need_id, $r->need->id);
    }

    public function test_from_user_relationship(): void
    {
        $r = $this->makeRating();
        $this->assertInstanceOf(User::class, $r->fromUser);
        $this->assertSame($r->from_user_id, $r->fromUser->id);
    }

    public function test_to_user_relationship(): void
    {
        $r = $this->makeRating();
        $this->assertInstanceOf(User::class, $r->toUser);
        $this->assertSame($r->to_user_id, $r->toUser->id);
    }

    public function test_scope_valid_returns_all_scores_in_range(): void
    {
        $requester = User::factory()->create();
        $provider = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id' => $this->makeCategory(),
            'title' => 'Test Need',
            'description' => 'Test Description',
            'status' => 'OPEN',
        ]);

        for ($score = 1; $score <= 5; $score++) {
            $distinctProvider = User::factory()->create();
            Rating::create([
                'need_id' => $need->id,
                'from_user_id' => $requester->id,
                'to_user_id' => $distinctProvider->id,
                'score' => $score,
                'created_at' => now(),
            ]);
        }

        $valid = Rating::valid()->get();
        $this->assertCount(5, $valid);
    }

    public function test_review_is_nullable(): void
    {
        $r = $this->makeRating(['review' => null]);
        $r->refresh();
        $this->assertNull($r->review);
    }

    public function test_multiple_ratings_for_same_need(): void
    {
        $requester = User::factory()->create();
        $provider1 = User::factory()->create();
        $provider2 = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id' => $this->makeCategory(),
            'title' => 'Test Need',
            'description' => 'Test Description',
            'status' => 'OPEN',
        ]);

        Rating::create([
            'need_id' => $need->id,
            'from_user_id' => $requester->id,
            'to_user_id' => $provider1->id,
            'score' => 5,
            'created_at' => now(),
        ]);
        Rating::create([
            'need_id' => $need->id,
            'from_user_id' => $requester->id,
            'to_user_id' => $provider2->id,
            'score' => 4,
            'created_at' => now(),
        ]);

        $this->assertSame(2, Rating::where('need_id', $need->id)->count());
    }
}
