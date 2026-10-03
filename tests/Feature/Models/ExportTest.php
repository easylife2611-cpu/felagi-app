<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Comparison;
use App\Models\Export;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $e = Export::factory()->create();
        $this->assertTrue(Str::isUuid($e->id));
    }

    public function test_status_constants_match(): void
    {
        $this->assertSame('PENDING',    Export::STATUS_PENDING);
        $this->assertSame('PROCESSING', Export::STATUS_PROCESSING);
        $this->assertSame('READY',      Export::STATUS_READY);
        $this->assertSame('FAILED',     Export::STATUS_FAILED);
        $this->assertSame('EXPIRED',    Export::STATUS_EXPIRED);
    }

    public function test_format_constants_match(): void
    {
        $this->assertSame('PDF',  Export::FORMAT_PDF);
        $this->assertSame('XLSX', Export::FORMAT_XLSX);
        $this->assertSame('CSV',  Export::FORMAT_CSV);
        $this->assertSame('TXT',  Export::FORMAT_TXT);
    }

    public function test_default_status_is_pending(): void
    {
        $e = Export::factory()->create();
        $this->assertSame(Export::STATUS_PENDING, $e->fresh()->status);
    }

    public function test_ready_state_sets_storage_key_and_expires_at(): void
    {
        $e = Export::factory()->ready()->create();
        $fresh = $e->fresh();
        $this->assertSame(Export::STATUS_READY, $fresh->status);
        $this->assertNotNull($fresh->storage_key);
        $this->assertNotNull($fresh->expires_at);
        $this->assertNotNull($fresh->completed_at);
    }

    public function test_processing_state(): void
    {
        $e = Export::factory()->processing()->create();
        $this->assertSame(Export::STATUS_PROCESSING, $e->fresh()->status);
    }

    public function test_failed_state(): void
    {
        $e = Export::factory()->failed()->create();
        $this->assertSame(Export::STATUS_FAILED, $e->fresh()->status);
    }

    public function test_expired_state(): void
    {
        $e = Export::factory()->expired()->create();
        $fresh = $e->fresh();
        $this->assertSame(Export::STATUS_EXPIRED, $fresh->status);
        $this->assertTrue($fresh->expires_at->isPast());
    }

    public function test_format_state(): void
    {
        $e = Export::factory()->format(Export::FORMAT_CSV)->create();
        $this->assertSame(Export::FORMAT_CSV, $e->fresh()->format);
    }

    public function test_expires_at_casts_to_carbon(): void
    {
        $e = Export::factory()->ready()->create();
        $this->assertInstanceOf(Carbon::class, $e->fresh()->expires_at);
    }

    public function test_created_at_casts_to_carbon(): void
    {
        $e = Export::factory()->create();
        $this->assertInstanceOf(Carbon::class, $e->fresh()->created_at);
    }

    public function test_completed_at_casts_to_carbon(): void
    {
        $e = Export::factory()->ready()->create();
        $this->assertInstanceOf(Carbon::class, $e->fresh()->completed_at);
    }

    public function test_comparison_id_resolves(): void
    {
        $c = Comparison::factory()->create();
        $e = Export::factory()->create(['comparison_id' => $c->id]);
        $this->assertSame($c->id, $e->fresh()->comparison_id);
    }

    public function test_requester_id_resolves(): void
    {
        $u = User::factory()->create();
        $e = Export::factory()->create(['requester_id' => $u->id]);
        $this->assertSame($u->id, $e->fresh()->requester_id);
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $e = new Export();
        foreach ([
            'comparison_id', 'requester_id', 'format', 'status',
            'storage_key', 'expires_at', 'created_at', 'completed_at',
        ] as $f) {
            $this->assertContains($f, $e->getFillable());
        }
    }

    public function test_timestamps_disabled(): void
    {
        $e = new Export();
        $this->assertFalse($e->timestamps);
    }

    public function test_storage_key_nullable_by_default(): void
    {
        $e = Export::factory()->create();
        $this->assertNull($e->fresh()->storage_key);
    }
}
