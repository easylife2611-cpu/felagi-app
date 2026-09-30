<?php

namespace Tests\Feature\Models;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * WP-B27 / R-TEST-05 — Report model unit tests.
 * Schema: 2026_09_29_000018. No HasFactory.
 * NOTE: entity_id has no FK (polymorphic-style).
 */
class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function makeReport(array $overrides = []): Report
    {
        $reporter = User::factory()->create();

        return Report::create(array_merge([
            'reporter_id' => $reporter->id,
            'entity_type' => Report::ENTITY_NEED,
            'entity_id'   => (string) Str::uuid(),
            'reason_code' => Report::REASON_SPAM,
            'details'     => 'Spam content',
            'status'      => Report::STATUS_OPEN,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $r = $this->makeReport();
        $this->assertNotNull($r->id);
        $this->assertTrue(Str::isUuid($r->id));
    }

    public function test_persists_core_attributes(): void
    {
        $r = $this->makeReport([
            'reason_code' => 'FRAUD',
            'entity_type' => 'USER',
        ]);

        $this->assertDatabaseHas('reports', [
            'id'          => $r->id,
            'reason_code' => 'FRAUD',
            'entity_type' => 'USER',
            'status'      => 'OPEN',
        ]);
    }

    public function test_belongs_to_reporter(): void
    {
        $r = $this->makeReport();
        $this->assertInstanceOf(User::class, $r->reporter);
        $this->assertSame($r->reporter_id, $r->reporter->id);
    }

    public function test_assigned_to_nullable(): void
    {
        $r = $this->makeReport(['assigned_to' => null]);
        $this->assertNull($r->fresh()->assigned_to);
        $this->assertNull($r->fresh()->assignee);
    }

    public function test_belongs_to_assignee_when_set(): void
    {
        $admin = User::factory()->create();
        $r = $this->makeReport(['assigned_to' => $admin->id]);

        $this->assertInstanceOf(User::class, $r->fresh()->assignee);
        $this->assertSame($admin->id, $r->fresh()->assignee->id);
    }

    public function test_details_nullable(): void
    {
        $r = $this->makeReport(['details' => null]);
        $this->assertNull($r->fresh()->details);
    }

    public function test_resolution_code_nullable(): void
    {
        $r = $this->makeReport(['resolution_code' => null]);
        $this->assertNull($r->fresh()->resolution_code);
    }

    public function test_entity_type_constants(): void
    {
        $this->assertSame('NEED', Report::ENTITY_NEED);
        $this->assertSame('OFFER', Report::ENTITY_OFFER);
        $this->assertSame('MESSAGE', Report::ENTITY_MESSAGE);
        $this->assertSame('USER', Report::ENTITY_USER);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('OPEN', Report::STATUS_OPEN);
        $this->assertSame('IN_REVIEW', Report::STATUS_IN_REVIEW);
        $this->assertSame('RESOLVED', Report::STATUS_RESOLVED);
        $this->assertSame('DISMISSED', Report::STATUS_DISMISSED);
    }

    public function test_reason_constants(): void
    {
        $this->assertSame('SAFETY', Report::REASON_SAFETY);
        $this->assertSame('FRAUD', Report::REASON_FRAUD);
        $this->assertSame('SPAM', Report::REASON_SPAM);
        $this->assertSame('OTHER', Report::REASON_OTHER);
    }

    public function test_default_status_is_open(): void
    {
        $reporter = User::factory()->create();
        $r = Report::create([
            'reporter_id' => $reporter->id,
            'entity_type' => 'NEED',
            'entity_id'   => (string) Str::uuid(),
            'reason_code' => 'SPAM',
        ]);
        $this->assertSame('OPEN', $r->fresh()->status);
    }

    public function test_scope_open_filters(): void
    {
        $this->makeReport(['status' => 'OPEN']);
        $this->makeReport(['status' => 'OPEN']);
        $this->makeReport(['status' => 'IN_REVIEW']);
        $this->makeReport(['status' => 'RESOLVED']);

        $this->assertSame(2, Report::open()->count());
    }

    public function test_multiple_reports_for_same_entity_allowed(): void
    {
        $entityId = (string) Str::uuid();
        $this->makeReport(['entity_id' => $entityId]);
        $this->makeReport(['entity_id' => $entityId]);

        $this->assertSame(2, Report::where('entity_id', $entityId)->count());
    }

    public function test_entity_id_has_no_fk_constraint(): void
    {
        // entity_id can be any UUID (polymorphic-style) — no FK
        $random = (string) Str::uuid();
        $r = $this->makeReport(['entity_id' => $random]);
        $this->assertSame($random, $r->fresh()->entity_id);
    }

    public function test_uses_timestamps(): void
    {
        $this->assertTrue((new Report())->usesTimestamps());
    }

    public function test_created_at_populated(): void
    {
        $r = $this->makeReport();
        $this->assertNotNull($r->fresh()->created_at);
    }

    public function test_resolution_code_stored_when_resolved(): void
    {
        $admin = User::factory()->create();
        $r = $this->makeReport([
            'status'          => 'RESOLVED',
            'assigned_to'     => $admin->id,
            'resolution_code' => 'ACTION_TAKEN',
        ]);
        $this->assertSame('ACTION_TAKEN', $r->fresh()->resolution_code);
    }
}
