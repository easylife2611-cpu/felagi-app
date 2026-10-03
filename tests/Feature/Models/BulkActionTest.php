<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\BulkAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

final class BulkActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $b = BulkAction::factory()->create();
        $this->assertTrue(Str::isUuid($b->id));
    }

    public function test_status_constants_match(): void
    {
        $this->assertSame('PREVIEWED', BulkAction::STATUS_PREVIEWED);
        $this->assertSame('EXECUTED', BulkAction::STATUS_EXECUTED);
        $this->assertSame('PARTIAL', BulkAction::STATUS_PARTIAL);
        $this->assertSame('FAILED', BulkAction::STATUS_FAILED);
    }

    public function test_item_status_constants_match(): void
    {
        $this->assertSame('SUCCEEDED', BulkAction::ITEM_SUCCEEDED);
        $this->assertSame('FAILED', BulkAction::ITEM_FAILED);
        $this->assertSame('UNKNOWN', BulkAction::ITEM_UNKNOWN);
    }

    public function test_default_status_is_previewed(): void
    {
        $b = BulkAction::factory()->create();
        $this->assertSame(BulkAction::STATUS_PREVIEWED, $b->fresh()->status);
    }

    public function test_selection_ids_casts_to_array(): void
    {
        $b = BulkAction::factory()->create(['selection_ids' => ['a', 'b', 'c']]);
        $fresh = $b->fresh();
        $this->assertIsArray($fresh->selection_ids);
        $this->assertSame(['a', 'b', 'c'], $fresh->selection_ids);
    }

    public function test_items_casts_to_array(): void
    {
        $b = BulkAction::factory()->executed()->create();
        $this->assertIsArray($b->fresh()->items);
    }

    public function test_expected_count_casts_to_integer(): void
    {
        $b = BulkAction::factory()->create(['expected_count' => 5]);
        $this->assertSame(5, $b->fresh()->expected_count);
    }

    public function test_created_at_casts_to_carbon(): void
    {
        $b = BulkAction::factory()->create();
        $this->assertInstanceOf(Carbon::class, $b->fresh()->created_at);
    }

    public function test_belongs_to_actor(): void
    {
        $u = User::factory()->create();
        $b = BulkAction::factory()->create(['actor_id' => $u->id]);
        $this->assertSame($u->id, $b->fresh()->actor->id);
    }

    public function test_retry_of_relationship(): void
    {
        $original = BulkAction::factory()->executed()->create();
        $retry = BulkAction::factory()->retryOf($original)->create();
        $this->assertSame($original->id, $retry->fresh()->retryOf->id);
    }

    public function test_retry_of_null_by_default(): void
    {
        $b = BulkAction::factory()->create();
        $this->assertNull($b->fresh()->retry_of_id);
    }

    public function test_is_fully_succeeded_true_for_executed(): void
    {
        $b = BulkAction::factory()->executed()->create();
        $this->assertTrue($b->fresh()->isFullySucceeded());
    }

    public function test_is_fully_succeeded_false_for_partial(): void
    {
        $b = BulkAction::factory()->partial()->create();
        $this->assertFalse($b->fresh()->isFullySucceeded());
    }

    public function test_is_fully_succeeded_false_for_failed(): void
    {
        $b = BulkAction::factory()->failed()->create();
        $this->assertFalse($b->fresh()->isFullySucceeded());
    }

    public function test_failed_items_empty_when_none(): void
    {
        $b = BulkAction::factory()->executed()->create();
        $this->assertSame([], $b->fresh()->failedItems());
    }

    public function test_failed_items_returns_failed_entries(): void
    {
        $b = BulkAction::factory()->partial()->create();
        $failed = $b->fresh()->failedItems();
        $this->assertCount(1, $failed);
        $this->assertSame('id-2', $failed[0]['id']);
        $this->assertSame(BulkAction::ITEM_FAILED, $failed[0]['status']);
    }

    public function test_failed_items_handles_null_items(): void
    {
        $b = BulkAction::factory()->create();
        $this->assertSame([], $b->fresh()->failedItems());
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $b = new BulkAction();
        foreach ([
            'actor_id', 'action_type', 'entity_type', 'scope',
            'selection_ids', 'selection_digest', 'expected_count',
            'status', 'items', 'retry_of_id',
            'created_at', 'executed_at', 'completed_at',
        ] as $f) {
            $this->assertContains($f, $b->getFillable());
        }
    }

    public function test_timestamps_disabled(): void
    {
        $b = new BulkAction();
        $this->assertFalse($b->timestamps);
    }

    public function test_updated_at_constant_is_null(): void
    {
        $this->assertNull(BulkAction::UPDATED_AT);
    }
}
