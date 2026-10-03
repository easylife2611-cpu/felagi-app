<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * L347-F — Need model unit tests (B18 item 6).
 */
class NeedTest extends TestCase
{
    use RefreshDatabase;

    private function makeNeed(array $overrides = []): Need
    {
        $user = User::factory()->create();
        $cat  = Category::create([
            'slug'       => 'need-cat-' . Str::lower(Str::random(6)),
            'name_am'    => 'ምድብ',
            'name_en'    => 'Cat',
            'active'     => true,
            'sort_order' => 1,
        ]);

        return Need::create(array_merge([
            'requester_id' => $user->id,
            'category_id'  => $cat->id,
            'title'        => 'Test Need ' . Str::random(4),
            'description'  => 'Description',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $n = $this->makeNeed();
        $this->assertNotNull($n->id);
        $this->assertTrue(Str::isUuid($n->id));
    }

    public function test_soft_deletes(): void
    {
        $n = $this->makeNeed();
        $n->delete();
        $this->assertSoftDeleted('needs', ['id' => $n->id]);
    }

    public function test_persists_core_attributes(): void
    {
        $n = $this->makeNeed([
            'title'       => 'Fix roof',
            'description' => 'Leaking roof',
            'budget_min'  => '100.00',
            'budget_max'  => '500.00',
            'currency'    => 'ETB',
            'quantity'    => '2.00',
        ]);

        $this->assertDatabaseHas('needs', [
            'id'         => $n->id,
            'title'      => 'Fix roof',
            'status'     => Need::STATUS_OPEN,
            'currency'   => 'ETB',
        ]);
    }

    public function test_budget_min_casts_decimal_2(): void
    {
        $n = $this->makeNeed(['budget_min' => '123.45']);
        $this->assertSame('123.45', $n->fresh()->budget_min);
    }

    public function test_budget_max_casts_decimal_2(): void
    {
        $n = $this->makeNeed(['budget_max' => '999.99']);
        $this->assertSame('999.99', $n->fresh()->budget_max);
    }

    public function test_quantity_casts_decimal_2(): void
    {
        $n = $this->makeNeed(['quantity' => '3.50']);
        $this->assertSame('3.50', $n->fresh()->quantity);
    }

    public function test_deadline_at_casts_datetime(): void
    {
        $n = $this->makeNeed(['deadline_at' => '2026-12-31 23:59:59']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $n->fresh()->deadline_at);
    }

    public function test_version_casts_integer(): void
    {
        $n = $this->makeNeed(['version' => 5]);
        $this->assertSame(5, $n->fresh()->version);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('OPEN', Need::STATUS_OPEN);
        $this->assertSame('IN_PROGRESS', Need::STATUS_IN_PROGRESS);
        $this->assertSame('COMPLETED', Need::STATUS_COMPLETED);
        $this->assertSame('CANCELLED', Need::STATUS_CANCELLED);
    }

    public function test_belongs_to_requester(): void
    {
        $user = User::factory()->create();
        $n = $this->makeNeed(['requester_id' => $user->id]);
        $this->assertInstanceOf(User::class, $n->requester);
        $this->assertSame($user->id, $n->requester->id);
    }

    public function test_belongs_to_category(): void
    {
        $n = $this->makeNeed();
        $this->assertInstanceOf(Category::class, $n->category);
    }

    public function test_has_many_offers(): void
    {
        $n = $this->makeNeed();
        $this->assertCount(0, $n->offers);
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $n->offers
        );
    }

    public function test_has_one_award_initially_null(): void
    {
        $n = $this->makeNeed();
        $this->assertNull($n->award);
    }

    public function test_has_many_comparisons(): void
    {
        $n = $this->makeNeed();
        $this->assertCount(0, $n->comparisons);
    }

    public function test_scope_open_filters(): void
    {
        $this->makeNeed(['status' => Need::STATUS_OPEN]);
        $this->makeNeed(['status' => Need::STATUS_OPEN]);
        $this->makeNeed(['status' => Need::STATUS_COMPLETED]);

        $this->assertSame(2, Need::open()->count());
    }

    public function test_scope_published_excludes_archived(): void
    {
        $this->makeNeed();
        $this->makeNeed();
        $archived = $this->makeNeed();
        $archived->update(['archived_at' => now()]);

        $this->assertSame(2, Need::published()->count());
    }

    public function test_is_open_returns_true_for_open(): void
    {
        $n = $this->makeNeed(['status' => Need::STATUS_OPEN]);
        $this->assertTrue($n->isOpen());
    }

    public function test_is_open_returns_false_for_completed(): void
    {
        $n = $this->makeNeed(['status' => Need::STATUS_COMPLETED]);
        $this->assertFalse($n->isOpen());
    }

    public function test_is_owned_by_returns_true_for_owner(): void
    {
        $user = User::factory()->create();
        $n = $this->makeNeed(['requester_id' => $user->id]);
        $this->assertTrue($n->isOwnedBy($user));
    }

    public function test_is_owned_by_returns_false_for_other(): void
    {
        $n = $this->makeNeed();
        $other = User::factory()->create();
        $this->assertFalse($n->isOwnedBy($other));
    }
}
