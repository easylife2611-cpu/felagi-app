<?php

namespace Tests\Feature\Privacy;

use App\Models\ConsentLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_grant_creates_active_record(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/consent/grant', [
            'type'   => ConsentLog::TYPE_MARKETING,
            'source' => 'settings',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('consent_logs', [
            'user_id'      => $user->id,
            'consent_type' => ConsentLog::TYPE_MARKETING,
            'granted'      => true,
        ]);
    }

    public function test_revoke_marks_record(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson('/api/v1/consent/grant', [
            'type'   => ConsentLog::TYPE_MARKETING,
            'source' => 'settings',
        ])->assertCreated();

        $this->postJson('/api/v1/consent/revoke', [
            'type'   => ConsentLog::TYPE_MARKETING,
            'source' => 'settings',
        ])->assertOk();

        $this->assertDatabaseHas('consent_logs', [
            'user_id'      => $user->id,
            'consent_type' => ConsentLog::TYPE_MARKETING,
            'granted'      => false,
        ]);
    }

    public function test_index_returns_all_types(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/v1/consent');
        $response->assertOk()
            ->assertJsonStructure(['data' => [
                ConsentLog::TYPE_MARKETING,
                ConsentLog::TYPE_ADS,
                ConsentLog::TYPE_AI_COMPARE,
                ConsentLog::TYPE_TELEGRAM,
                ConsentLog::TYPE_CROSS_BORDER,
            ]]);
    }

    public function test_revoke_without_grant_returns_404(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson('/api/v1/consent/revoke', [
            'type'   => ConsentLog::TYPE_ADS,
            'source' => 'api',
        ])->assertNotFound();
    }

    public function test_grant_requires_valid_type(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson('/api/v1/consent/grant', [
            'type'   => 'invalid_type',
            'source' => 'api',
        ])->assertStatus(422);
    }

    public function test_consent_service_has_check(): void
    {
        $user = User::factory()->create();
        $service = app(\App\Services\Consent\ConsentService::class);

        $this->assertFalse($service->has($user, ConsentLog::TYPE_ADS));
        $service->grant($user, ConsentLog::TYPE_ADS, 'test');
        $this->assertTrue($service->has($user, ConsentLog::TYPE_ADS));
    }
}
