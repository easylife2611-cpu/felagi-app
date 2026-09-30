<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * WP-B27 / R-TEST-03 — Category model unit tests.
 * Schema: 2026_09_29_000002. No HasFactory, no SoftDeletes.
 */
class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'slug'       => 'cat-' . Str::lower(Str::random(8)),
            'name_am'    => 'ምድብ ' . Str::random(4),
            'name_en'    => 'Category ' . Str::random(4),
            'active'     => true,
            'sort_order' => 0,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $c = $this->makeCategory();
        $this->assertNotNull($c->id);
        $this->assertTrue(Str::isUuid($c->id));
    }

    public function test_persists_core_attributes(): void
    {
        $c = $this->makeCategory([
            'slug'       => 'electronics',
            'name_am'    => 'ኤሌክትሮኒክስ',
            'name_en'    => 'Electronics',
            'sort_order' => 10,
        ]);

        $this->assertDatabaseHas('categories', [
            'id'         => $c->id,
            'slug'       => 'electronics',
            'name_en'    => 'Electronics',
            'sort_order' => 10,
            'active'     => true,
        ]);
    }

    public function test_active_casts_to_boolean(): void
    {
        $c = $this->makeCategory(['active' => true]);
        $this->assertTrue($c->fresh()->active);

        $c2 = $this->makeCategory(['active' => false, 'slug' => 'x-' . Str::random(6)]);
        $this->assertFalse($c2->fresh()->active);
    }

    public function test_sort_order_casts_integer(): void
    {
        $c = $this->makeCategory(['sort_order' => 5]);
        $this->assertSame(5, $c->fresh()->sort_order);
    }

    public function test_default_active_is_true(): void
    {
        $c = Category::create([
            'slug'    => 'default-active-' . Str::random(6),
            'name_am' => 'ምድብ',
            'name_en' => 'Cat',
        ]);
        $this->assertTrue($c->fresh()->active);
    }

    public function test_default_sort_order_is_zero(): void
    {
        $c = Category::create([
            'slug'    => 'default-sort-' . Str::random(6),
            'name_am' => 'ምድብ',
            'name_en' => 'Cat',
        ]);
        $this->assertSame(0, $c->fresh()->sort_order);
    }

    public function test_belongs_to_creator(): void
    {
        $user = User::factory()->create();
        $c = $this->makeCategory(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $c->creator);
        $this->assertSame($user->id, $c->creator->id);
    }

    public function test_creator_nullable(): void
    {
        $c = $this->makeCategory(['created_by' => null]);
        $this->assertNull($c->fresh()->creator);
    }

    public function test_has_many_needs(): void
    {
        $c = $this->makeCategory();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $c->needs
        );
        $this->assertCount(0, $c->needs);
    }

    public function test_scope_active_filters(): void
    {
        $this->makeCategory(['active' => true]);
        $this->makeCategory(['active' => true]);
        $this->makeCategory(['active' => false]);

        $this->assertSame(2, Category::active()->count());
    }

    public function test_scope_ordered_sorts_by_sort_order_then_slug(): void
    {
        $this->makeCategory(['slug' => 'b', 'sort_order' => 2]);
        $this->makeCategory(['slug' => 'a', 'sort_order' => 1]);
        $this->makeCategory(['slug' => 'c', 'sort_order' => 2]);

        $slugs = Category::ordered()->pluck('slug')->all();
        $this->assertSame(['a', 'b', 'c'], $slugs);
    }

    public function test_name_returns_amharic_by_default(): void
    {
        $c = $this->makeCategory(['name_am' => 'ስም አማ', 'name_en' => 'Name EN']);
        $this->assertSame('ስም አማ', $c->name());
    }

    public function test_name_returns_en_for_en_locale(): void
    {
        $c = $this->makeCategory(['name_am' => 'ስም አማ', 'name_en' => 'Name EN']);
        $this->assertSame('Name EN', $c->name('en'));
    }

    public function test_slug_unique_constraint(): void
    {
        $this->makeCategory(['slug' => 'unique-slug-x']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->makeCategory(['slug' => 'unique-slug-x']);
    }

    public function test_slug_max_length_60(): void
    {
        // 60-char slug — should succeed
        $slug = Str::random(60);
        $c = $this->makeCategory(['slug' => $slug]);
        $this->assertSame($slug, $c->fresh()->slug);
    }
}
