<?php

namespace Tests\Feature\Models;

use App\Models\Comparison;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B26 / R-TEST-02 — Comparison model unit tests.
 * Schema: 2026_09_29_000006. No HasFactory.
 */
class ComparisonTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makeNeed(): Need
    {
        $requester = User::factory()->create();

        return Need::create([
            'requester_id' => $requester->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need',
            'description'  => 'Test Description',
            'status'       => 'OPEN',
        ]);
    }

    private function makeComparison(array $overrides = []): Comparison
    {
        $need = $this->makeNeed();

        return Comparison::create(array_merge([
            'need_id'             => $need->id,
            'version_number'      => 1,
            'triggered_by'        => $need->requester_id,
            'status'              => Comparison::STATUS_PENDING,
            'criteria_version'    => 'v1',
            'prompt_version'      => 'p1',
            'output_schema_version' => 's1',
            'ai_provider'         => 'openai',
            'model_id'            => 'gpt-4',
            'need_snapshot'       => ['title' => 'Test Need', 'budget' => 1000],
            'snapshot_hash'       => hash('sha256', 'test-snapshot'),
            'eligible_offer_count'=> 5,
            'included_offer_count'=> 3,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $c = $this->makeComparison();
        $this->assertNotNull($c->id);
        $this->assertTrue(Str::isUuid($c->id));
    }

    public function test_persists_core_attributes(): void
    {
        $c = $this->makeComparison(['version_number' => 2]);

        $this->assertDatabaseHas('comparisons', [
            'id'             => $c->id,
            'version_number' => 2,
            'status'         => 'PENDING',
            'ai_provider'    => 'openai',
        ]);
    }

    public function test_belongs_to_need(): void
    {
        $c = $this->makeComparison();
        $this->assertInstanceOf(Need::class, $c->need);
        $this->assertSame($c->need_id, $c->need->id);
    }

    public function test_belongs_to_triggered_by_user(): void
    {
        $c = $this->makeComparison();
        $this->assertInstanceOf(User::class, $c->triggeredBy);
        $this->assertSame($c->triggered_by, $c->triggeredBy->id);
    }

    public function test_has_many_comparison_offers(): void
    {
        $c = $this->makeComparison();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $c->comparisonOffers
        );
    }

    public function test_has_many_results(): void
    {
        $c = $this->makeComparison();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $c->results
        );
    }

    public function test_has_many_attempts(): void
    {
        $c = $this->makeComparison();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $c->attempts
        );
    }

    public function test_need_snapshot_casts_to_array(): void
    {
        $c = $this->makeComparison([
            'need_snapshot' => ['title' => 'X', 'budget' => 500, 'tags' => ['a', 'b']],
        ]);
        $fresh = $c->fresh();

        $this->assertIsArray($fresh->need_snapshot);
        $this->assertSame('X', $fresh->need_snapshot['title']);
        $this->assertSame(['a', 'b'], $fresh->need_snapshot['tags']);
    }

    public function test_version_number_casts_integer(): void
    {
        $c = $this->makeComparison(['version_number' => 5]);
        $this->assertSame(5, $c->fresh()->version_number);
    }

    public function test_offer_counts_cast_integer(): void
    {
        $c = $this->makeComparison([
            'eligible_offer_count' => 10,
            'included_offer_count' => 7,
        ]);
        $this->assertSame(10, $c->fresh()->eligible_offer_count);
        $this->assertSame(7, $c->fresh()->included_offer_count);
    }

    public function test_estimated_cost_casts_decimal_4(): void
    {
        $c = $this->makeComparison(['estimated_cost' => '1.2345']);
        $this->assertSame('1.2345', (string) $c->fresh()->estimated_cost);
    }

    public function test_estimated_cost_nullable(): void
    {
        $c = $this->makeComparison(['estimated_cost' => null]);
        $this->assertNull($c->fresh()->estimated_cost);
    }

    public function test_requested_at_casts_to_datetime(): void
    {
        $c = $this->makeComparison();
        $this->assertInstanceOf(\Carbon\Carbon::class, $c->fresh()->requested_at);
    }

    public function test_completed_at_casts_to_datetime(): void
    {
        $c = $this->makeComparison(['completed_at' => '2026-01-01 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $c->fresh()->completed_at);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('PENDING', Comparison::STATUS_PENDING);
        $this->assertSame('PROCESSING', Comparison::STATUS_PROCESSING);
        $this->assertSame('COMPLETED', Comparison::STATUS_COMPLETED);
        $this->assertSame('FAILED', Comparison::STATUS_FAILED);
    }

    public function test_default_status_is_pending(): void
    {
        $need = $this->makeNeed();
        $c = Comparison::create([
            'need_id'        => $need->id,
            'version_number' => 1,
            'triggered_by'   => $need->requester_id,
            'need_snapshot'  => ['x' => 1],
        ]);
        $this->assertSame('PENDING', $c->fresh()->status);
    }

    public function test_scope_completed_filters(): void
    {
        $this->makeComparison(['status' => 'COMPLETED']);
        $this->makeComparison(['version_number' => 2, 'status' => 'COMPLETED']);
        $this->makeComparison(['version_number' => 3, 'status' => 'PENDING']);
        $this->makeComparison(['version_number' => 4, 'status' => 'FAILED']);

        $this->assertSame(2, Comparison::completed()->count());
    }

    public function test_is_completed_returns_true_for_completed(): void
    {
        $this->assertTrue($this->makeComparison(['status' => 'COMPLETED'])->isCompleted());
    }

    public function test_is_completed_returns_false_for_pending(): void
    {
        $this->assertFalse($this->makeComparison(['status' => 'PENDING'])->isCompleted());
    }

    public function test_is_failed_returns_true_for_failed(): void
    {
        $this->assertTrue($this->makeComparison(['status' => 'FAILED'])->isFailed());
    }

    public function test_unique_need_id_and_version_number(): void
    {
        $need = $this->makeNeed();
        Comparison::create([
            'need_id'        => $need->id,
            'version_number' => 1,
            'triggered_by'   => $need->requester_id,
            'need_snapshot'  => ['x' => 1],
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Comparison::create([
            'need_id'        => $need->id,
            'version_number' => 1,
            'triggered_by'   => $need->requester_id,
            'need_snapshot'  => ['x' => 2],
        ]);
    }

    public function test_eligible_offer_count_defaults_zero(): void
    {
        $need = $this->makeNeed();
        $c = Comparison::create([
            'need_id'        => $need->id,
            'version_number' => 1,
            'triggered_by'   => $need->requester_id,
            'need_snapshot'  => ['x' => 1],
        ]);
        $this->assertSame(0, $c->fresh()->eligible_offer_count);
        $this->assertSame(0, $c->fresh()->included_offer_count);
    }

    public function test_attempt_count_defaults_zero(): void
    {
        $c = $this->makeComparison();
        $this->assertSame(0, $c->fresh()->attempt_count);
    }
}
