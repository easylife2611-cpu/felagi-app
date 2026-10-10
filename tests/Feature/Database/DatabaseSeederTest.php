<?php

namespace Tests\Feature\Database;

use App\Models\BoostPackage;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\BoostPackageSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ControlRegistrySeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GAP-43 regression guard — DatabaseSeeder + UserFactory alignment.
 *
 * Background:
 *   GAP-43 ("DatabaseSeeder schema mismatch | HIGH") was registered
 *   in WP-13 (7b97c36) but never closed after the fix shipped in the
 *   same commit. These tests prevent a repeat of the underlying issue
 *   (UserFactory drifting from the users table schema) and prove the
 *   seeder is idempotent.
 */
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function database_seeder_runs_without_errors(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertGreaterThan(0, Setting::count());
    }

    /** @test */
    public function database_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $first = $this->snapshotCounts();

        $this->seed(DatabaseSeeder::class);
        $second = $this->snapshotCounts();

        $this->assertSame($first, $second, 'Seeder must be idempotent');
    }

    /** @test */
    public function control_registry_seeder_creates_32_canonical_settings(): void
    {
        $this->seed(ControlRegistrySeeder::class);

        $this->assertSame(32, Setting::count());
        $this->assertNotNull(Setting::find('feature.needs'));
        $this->assertNotNull(Setting::find('feature.payments'));
        $this->assertNotNull(Setting::find('system.safe_mode'));
        $this->assertNotNull(Setting::find('boostPackages'));
    }

    /** @test */
    public function category_seeder_creates_10_canonical_categories(): void
    {
        $this->seed(CategorySeeder::class);

        $this->assertSame(10, Category::count());

        $canonicalSlugs = [
            'transport', 'construction', 'it-software', 'agriculture',
            'manufacturing', 'services', 'education', 'healthcare',
            'logistics', 'consulting',
        ];

        foreach ($canonicalSlugs as $slug) {
            $this->assertDatabaseHas('categories', ['slug' => $slug, 'active' => true]);
        }
    }

    /** @test */
    public function boost_package_seeder_creates_1_3_7_day_packages(): void
    {
        $this->seed(BoostPackageSeeder::class);

        $this->assertSame(3, BoostPackage::count());

        $this->assertDatabaseHas('boost_packages', [
            'duration_days' => 1, 'price' => 25.00, 'currency' => 'ETB', 'active' => true,
        ]);
        $this->assertDatabaseHas('boost_packages', [
            'duration_days' => 3, 'price' => 49.00, 'currency' => 'ETB', 'active' => true,
        ]);
        $this->assertDatabaseHas('boost_packages', [
            'duration_days' => 7, 'price' => 99.00, 'currency' => 'ETB', 'active' => true,
        ]);
    }

    /**
     * GAP-43 regression test — UserFactory MUST match the users table schema.
     *
     * Prior failure mode: factory emitted 'name'/'email'/'password' but the
     * users table required 'telegram_subject'/'full_name' NOT NULL.
     *
     * @test
     */
    public function user_factory_is_aligned_with_users_schema(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->telegram_subject);
        $this->assertNotNull($user->full_name);
        $this->assertSame('ACTIVE', $user->status);
        $this->assertSame(0, $user->rating_count);
        $this->assertNull($user->email);
    }

    /** @test */
    public function user_factory_states_work(): void
    {
        $suspended = User::factory()->suspended()->create();
        $this->assertSame('SUSPENDED', $suspended->status);

        $banned = User::factory()->banned()->create();
        $this->assertSame('BANNED', $banned->status);
    }

    /**
     * @return array<string, int>
     */
    private function snapshotCounts(): array
    {
        return [
            'settings'       => Setting::count(),
            'categories'     => Category::count(),
            'boost_packages' => BoostPackage::count(),
        ];
    }
}
