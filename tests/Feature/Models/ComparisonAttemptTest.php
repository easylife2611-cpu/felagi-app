<?php

namespace Tests\Feature\Models;

use App\Models\Comparison;
use App\Models\ComparisonAttempt;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B26 / R-TEST-02 — ComparisonAttempt model unit tests.
 * Schema: 2026_09_29_000009. No HasFactory. No timestamps.
 */
class ComparisonAttemptTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makeComparison(): Comparison
    {
        $requester = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need',
            'description'  => 'Test Description',
            'status'       => 'OPEN',
        ]);

        return Comparison::create([
            'need_id'        => $need->id,
            'version_number' => 1,
            'triggered_by'   => $requester->id,
            'need_snapshot'  => ['title' => 'Test Need'],
        ]);
    }

    private function makeAttempt(array $overrides = []): ComparisonAttempt
    {
        return ComparisonAttempt::create(array_merge([
            'comparison_id'       => $this->makeComparison()->id,
            'attempt_number'      => 1,
            'status'              => ComparisonAttempt::STATUS_PROCESSING,
            'provider_request_id' => 'req-' . Str::random(8),
            'token_usage'         => ['prompt' => 100, 'completion' => 200],
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $this->assertTrue(Str::isUuid($this->makeAttempt()->id));
    }

    public function test_no_timestamps(): void
    {
        $this->assertFalse((new ComparisonAttempt())->usesTimestamps());
    }

    public function test_belongs_to_comparison(): void
    {
        $a = $this->makeAttempt();
        $this->assertInstanceOf(Comparison::class, $a->comparison);
        $this->assertSame($a->comparison_id, $a->comparison->id);
    }

    public function test_attempt_number_casts_integer(): void
    {
        $a = $this->makeAttempt(['attempt_number' => 3]);
        $this->assertSame(3, $a->fresh()->attempt_number);
    }

    public function test_started_at_casts_to_datetime(): void
    {
        $a = $this->makeAttempt();
        $this->assertInstanceOf(\Carbon\Carbon::class, $a->fresh()->started_at);
    }

    public function test_finished_at_casts_to_datetime(): void
    {
        $a = $this->makeAttempt(['finished_at' => '2026-01-01 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $a->fresh()->finished_at);
    }

    public function test_finished_at_nullable(): void
    {
        $a = $this->makeAttempt(['finished_at' => null]);
        $this->assertNull($a->fresh()->finished_at);
    }

    public function test_token_usage_casts_to_array(): void
    {
        $a = $this->makeAttempt([
            'token_usage' => ['prompt' => 50, 'completion' => 75, 'total' => 125],
        ]);
        $fresh = $a->fresh();

        $this->assertIsArray($fresh->token_usage);
        $this->assertSame(50, $fresh->token_usage['prompt']);
        $this->assertSame(125, $fresh->token_usage['total']);
    }

    public function test_token_usage_nullable(): void
    {
        $a = $this->makeAttempt(['token_usage' => null]);
        $this->assertNull($a->fresh()->token_usage);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('PROCESSING', ComparisonAttempt::STATUS_PROCESSING);
        $this->assertSame('SUCCEEDED', ComparisonAttempt::STATUS_SUCCEEDED);
        $this->assertSame('FAILED', ComparisonAttempt::STATUS_FAILED);
    }

    public function test_default_status_is_processing(): void
    {
        $comparison = $this->makeComparison();
        $a = ComparisonAttempt::create([
            'comparison_id'  => $comparison->id,
            'attempt_number' => 1,
        ]);
        $this->assertSame('PROCESSING', $a->fresh()->status);
    }

    public function test_failure_code_nullable(): void
    {
        $a = $this->makeAttempt(['failure_code' => null]);
        $this->assertNull($a->fresh()->failure_code);
    }

    public function test_failure_code_stored(): void
    {
        $a = $this->makeAttempt([
            'status'       => ComparisonAttempt::STATUS_FAILED,
            'failure_code' => 'TIMEOUT',
        ]);
        $this->assertSame('TIMEOUT', $a->fresh()->failure_code);
    }

    public function test_provider_request_id_nullable(): void
    {
        $a = $this->makeAttempt(['provider_request_id' => null]);
        $this->assertNull($a->fresh()->provider_request_id);
    }

    public function test_unique_comparison_id_and_attempt_number(): void
    {
        $comparison = $this->makeComparison();
        ComparisonAttempt::create([
            'comparison_id'  => $comparison->id,
            'attempt_number' => 1,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        ComparisonAttempt::create([
            'comparison_id'  => $comparison->id,
            'attempt_number' => 1,
        ]);
    }

    public function test_multiple_attempts_with_different_numbers(): void
    {
        $comparison = $this->makeComparison();
        ComparisonAttempt::create(['comparison_id' => $comparison->id, 'attempt_number' => 1]);
        ComparisonAttempt::create(['comparison_id' => $comparison->id, 'attempt_number' => 2]);
        ComparisonAttempt::create(['comparison_id' => $comparison->id, 'attempt_number' => 3]);

        $this->assertSame(3, ComparisonAttempt::where('comparison_id', $comparison->id)->count());
    }

    public function test_started_at_auto_set_by_db(): void
    {
        $comparison = $this->makeComparison();
        $a = ComparisonAttempt::create([
            'comparison_id'  => $comparison->id,
            'attempt_number' => 1,
        ]);
        $this->assertNotNull($a->fresh()->started_at);
    }
}
