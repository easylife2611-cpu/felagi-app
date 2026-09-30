<?php

namespace Tests\Feature\Report;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_auth(): void
    {
        $this->postJson('/api/v1/reports', [
            'reason_code' => 'SPAM',
            'entity_type' => 'USER',
            'entity_id'   => '00000000-0000-0000-0000-000000000000',
            'details'     => 'This is a test report with enough chars.',
        ])->assertStatus(401);
    }

    public function test_validates_required_fields(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u, 'sanctum')->postJson('/api/v1/reports', [])->assertStatus(422);
    }

    public function test_creates_report(): void
    {
        $u = User::factory()->create();
        $res = $this->actingAs($u, 'sanctum')->postJson('/api/v1/reports', [
            'reason_code' => 'FRAUD',
            'entity_type' => 'USER',
            'entity_id'   => '00000000-0000-0000-0000-000000000001',
            'details'     => 'This user asked for payment outside the platform multiple times.',
        ]);
        $res->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'reporter_id' => $u->id,
            'reason_code' => 'FRAUD',
        ]);
    }

    public function test_rejects_invalid_reason_code(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u, 'sanctum')->postJson('/api/v1/reports', [
            'reason_code' => 'BOGUS',
            'entity_type' => 'USER',
            'entity_id'   => '00000000-0000-0000-0000-000000000002',
            'details'     => 'This is a valid-length details for testing purposes.',
        ])->assertStatus(422);
    }

    public function test_rejects_invalid_entity_type(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u, 'sanctum')->postJson('/api/v1/reports', [
            'reason_code' => 'SPAM',
            'entity_type' => 'lowercase',
            'entity_id'   => '00000000-0000-0000-0000-000000000003',
            'details'     => 'This is a valid-length details for testing purposes.',
        ])->assertStatus(422);
    }

    public function test_rejects_non_uuid_entity_id(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u, 'sanctum')->postJson('/api/v1/reports', [
            'reason_code' => 'SPAM',
            'entity_type' => 'USER',
            'entity_id'   => 'not-a-uuid',
            'details'     => 'This is a valid-length details for testing purposes.',
        ])->assertStatus(422);
    }
}
