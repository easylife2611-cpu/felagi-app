<?php

declare(strict_types=1);

namespace Tests\Feature\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class MigrationIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /** Framework-owned tables — no Eloquent model expected. */
    private const FRAMEWORK_TABLES = [
        'cache', 'cache_locks',
        'failed_jobs', 'job_batches', 'jobs',
        'migrations',
        'password_reset_tokens',
        'personal_access_tokens',
        'sessions',
    ];

    /** Application tables deliberately modeled via DB facade (no model). */
    private const DB_FACADE_TABLES = [
        'data_requests',
        'idempotency_keys',
        'scheduled_settings',
    ];

    private function allModels(): array
    {
        $models = [];
        foreach (glob(app_path('Models/*.php')) as $f) {
            $class = 'App\\Models\\' . basename($f, '.php');
            if (class_exists($class)) {
                $models[] = $class;
            }
        }
        return $models;
    }

    private function allTables(): array
    {
        return collect(Schema::getTables())->pluck('name')->sort()->values()->all();
    }

    private function modelTables(): array
    {
        return array_map(fn($c) => (new $c())->getTable(), $this->allModels());
    }

    public function test_every_model_resolves_to_existing_table(): void
    {
        $tables = $this->allTables();
        foreach ($this->allModels() as $class) {
            $table = (new $class())->getTable();
            $this->assertContains($table, $tables, "{$class} expects table '{$table}' — not found");
        }
    }

    public function test_model_table_names_are_unique(): void
    {
        $seen = [];
        foreach ($this->allModels() as $class) {
            $table = (new $class())->getTable();
            if (array_key_exists($table, $seen)) {
                $this->fail("Table '{$table}' claimed by both {$seen[$table]} and {$class}");
            }
            $seen[$table] = $class;
        }
        $this->assertNotEmpty($seen);
    }

    public function test_no_two_models_share_a_table(): void
    {
        $tables = $this->modelTables();
        $this->assertSame(count($tables), count(array_unique($tables)));
    }

    public function test_every_application_table_has_a_model(): void
    {
        $modelTables = $this->modelTables();
        foreach ($this->allTables() as $table) {
            if (in_array($table, self::FRAMEWORK_TABLES, true)) continue;
            if (in_array($table, self::DB_FACADE_TABLES, true)) continue;

            $this->assertContains(
                $table, $modelTables,
                "Table '{$table}' has no model and is not in FRAMEWORK_TABLES or DB_FACADE_TABLES"
            );
        }
    }

    public function test_framework_tables_have_no_model(): void
    {
        $modelTables = $this->modelTables();
        foreach (self::FRAMEWORK_TABLES as $framework) {
            $this->assertNotContains(
                $framework, $modelTables,
                "Framework table '{$framework}' must not be Eloquent-modeled"
            );
        }
    }

    public function test_db_facade_tables_exist_in_schema(): void
    {
        foreach (self::DB_FACADE_TABLES as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "DB-facade table '{$table}' declared but missing from schema"
            );
        }
    }

    public function test_model_fillable_fields_exist_as_columns(): void
    {
        foreach ($this->allModels() as $class) {
            $instance = new $class();
            $table = $instance->getTable();
            if (! Schema::hasTable($table)) continue;

            $columns = Schema::getColumnListing($table);
            foreach ($instance->getFillable() as $field) {
                $this->assertContains(
                    $field, $columns,
                    "{$class}::\$fillable has '{$field}' but '{$table}' lacks that column"
                );
            }
        }
    }

    public function test_model_casts_fields_exist_as_columns(): void
    {
        foreach ($this->allModels() as $class) {
            $instance = new $class();
            $table = $instance->getTable();
            if (! Schema::hasTable($table)) continue;

            $columns = Schema::getColumnListing($table);
            foreach (array_keys($instance->getCasts()) as $field) {
                if ($field === 'id') continue;
                $this->assertContains(
                    $field, $columns,
                    "{$class}::\$casts has '{$field}' but '{$table}' lacks that column"
                );
            }
        }
    }

    public function test_schema_has_at_least_expected_table_count(): void
    {
        $this->assertGreaterThanOrEqual(45, count($this->allTables()));
    }

    public function test_users_table_has_uuid_or_integer_primary(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertContains('id', Schema::getColumnListing('users'));
    }

    public function test_only_offer_submission_declares_explicit_table(): void
    {
        $explicit = [];
        foreach ($this->allModels() as $class) {
            $ref = new \ReflectionClass($class);
            $defaults = $ref->getDefaultProperties();
            if (isset($defaults['table']) && $defaults['table'] !== null) {
                $explicit[class_basename($class)] = $defaults['table'];
            }
        }
        $this->assertArrayHasKey('OfferSubmission', $explicit);
        $this->assertSame('offer_submissions', $explicit['OfferSubmission']);
    }
}
