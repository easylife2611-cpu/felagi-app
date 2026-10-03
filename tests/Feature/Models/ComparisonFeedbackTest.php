<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Comparison;
use App\Models\ComparisonFeedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ComparisonFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $f = ComparisonFeedback::factory()->create();
        $this->assertTrue(Str::isUuid($f->id));
    }

    public function test_rating_constants_match(): void
    {
        $this->assertSame('FAIR',       ComparisonFeedback::RATING_FAIR);
        $this->assertSame('INACCURATE', ComparisonFeedback::RATING_INACCURATE);
        $this->assertSame('UNCLEAR',    ComparisonFeedback::RATING_UNCLEAR);
        $this->assertSame('OTHER',      ComparisonFeedback::RATING_OTHER);
    }

    public function test_ratings_array_contains_four(): void
    {
        $this->assertCount(4, ComparisonFeedback::RATINGS);
        $this->assertContains('FAIR',       ComparisonFeedback::RATINGS);
        $this->assertContains('INACCURATE', ComparisonFeedback::RATINGS);
        $this->assertContains('UNCLEAR',    ComparisonFeedback::RATINGS);
        $this->assertContains('OTHER',      ComparisonFeedback::RATINGS);
    }

    public function test_default_rating_is_fair(): void
    {
        $f = ComparisonFeedback::factory()->create();
        $this->assertSame(ComparisonFeedback::RATING_FAIR, $f->fresh()->rating);
    }

    public function test_fair_state(): void
    {
        $f = ComparisonFeedback::factory()->fair()->create();
        $this->assertSame(ComparisonFeedback::RATING_FAIR, $f->fresh()->rating);
    }

    public function test_inaccurate_state(): void
    {
        $f = ComparisonFeedback::factory()->inaccurate()->create();
        $this->assertSame(ComparisonFeedback::RATING_INACCURATE, $f->fresh()->rating);
    }

    public function test_unclear_state(): void
    {
        $f = ComparisonFeedback::factory()->unclear()->create();
        $this->assertSame(ComparisonFeedback::RATING_UNCLEAR, $f->fresh()->rating);
    }

    public function test_other_state(): void
    {
        $f = ComparisonFeedback::factory()->other()->create();
        $this->assertSame(ComparisonFeedback::RATING_OTHER, $f->fresh()->rating);
    }

    public function test_without_comment_state(): void
    {
        $f = ComparisonFeedback::factory()->withoutComment()->create();
        $this->assertNull($f->fresh()->comment);
    }

    public function test_belongs_to_comparison(): void
    {
        $c = Comparison::factory()->create();
        $f = ComparisonFeedback::factory()->create(['comparison_id' => $c->id]);
        $this->assertSame($c->id, $f->fresh()->comparison->id);
    }

    public function test_belongs_to_provider(): void
    {
        $u = User::factory()->create();
        $f = ComparisonFeedback::factory()->create(['provider_id' => $u->id]);
        $this->assertSame($u->id, $f->fresh()->provider->id);
    }

    public function test_unique_constraint_on_comparison_and_provider(): void
    {
        $c = Comparison::factory()->create();
        $u = User::factory()->create();

        ComparisonFeedback::factory()->create(['comparison_id' => $c->id, 'provider_id' => $u->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        ComparisonFeedback::factory()->create(['comparison_id' => $c->id, 'provider_id' => $u->id]);
    }

    public function test_default_status_is_submitted(): void
    {
        $f = ComparisonFeedback::factory()->create();
        $this->assertSame('SUBMITTED', $f->fresh()->status);
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $f = new ComparisonFeedback();
        foreach (['comparison_id', 'provider_id', 'rating', 'comment', 'status'] as $field) {
            $this->assertContains($field, $f->getFillable());
        }
    }

    public function test_comment_can_be_set(): void
    {
        $f = ComparisonFeedback::factory()->create(['comment' => 'Too vague']);
        $this->assertSame('Too vague', $f->fresh()->comment);
    }
}
