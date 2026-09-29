<?php

namespace Tests\Feature\Admin;

use App\Models\TelegramDestination;
use App\Models\TelegramPublication;
use App\Models\TelegramPublicationEvent;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TelegramFoundationTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => 'MAIN_ADMIN']);
        return $u;
    }

    /**
     * Create destination with schema-correct fields.
     *
     * Schema notes:
     *   telegram_chat_id      = bigint (NOT string)
     *   permission_evidence   = NOT NULL
     *   type                  = OWNED_CHANNEL|PARTNER_CHANNEL|PARTNER_SUPERGROUP
     */
    private function makeDestination(array $overrides = []): TelegramDestination
    {
        $u = $this->adminUser();

        $unique = random_int(1000000, 9999999);

        return TelegramDestination::create(array_merge([
            'telegram_chat_id'    => -1000000000000 + $unique,  // Telegram chat IDs are negative bigint
            'public_username'     => '@test_channel_' . $unique,
            'type'                => TelegramDestination::TYPE_OWNED_CHANNEL,
            'name'                => 'Test Channel',
            'allowed_category_ids'=> [],
            'permission_evidence' => ['granted_by' => 'test', 'evidence' => 'unit_test'],
            'status'              => TelegramDestination::STATUS_ACTIVE,
            'daily_cap'           => 10,
            'quiet_hours'         => null,
            'created_by'          => $u->id,
        ], $overrides));
    }

    // ─── Model tests ───

    /** T01: TelegramDestination create + basic fields */
    public function test_destination_create(): void
    {
        $dest = $this->makeDestination();

        $this->assertNotNull($dest->id);
        $this->assertEquals('Test Channel', $dest->name);
        $this->assertEquals('OWNED_CHANNEL', $dest->type);
        $this->assertEquals('ACTIVE', $dest->status);
    }

    /** T02: scopeActive() */
    public function test_destination_scope_active(): void
    {
        $this->makeDestination(['status' => 'ACTIVE']);
        $this->makeDestination(['status' => 'PAUSED']);

        $this->assertEquals(1, TelegramDestination::active()->count());
    }

    /** T03: isExpired() + canPublish() */
    public function test_destination_can_publish(): void
    {
        $active = $this->makeDestination();
        $this->assertTrue($active->canPublish());

        $expired = $this->makeDestination([
            'expires_at' => now()->subDay(),
        ]);
        $this->assertFalse($expired->canPublish());

        $paused = $this->makeDestination([
            'status' => 'PAUSED',
        ]);
        $this->assertFalse($paused->canPublish());
    }

    /** T04: TelegramPublication state constants */
    public function test_publication_state_constants(): void
    {
        $this->assertEquals('QUEUED', TelegramPublication::STATE_QUEUED);
        $this->assertEquals('POSTED', TelegramPublication::STATE_POSTED);
        $this->assertEquals('UNCERTAIN', TelegramPublication::STATE_UNCERTAIN);
        $this->assertEquals('RETRY', TelegramPublication::STATE_RETRY);
    }

    /** T05: TelegramPublicationEvent append-only (no timestamps) */
    public function test_event_no_timestamps(): void
    {
        $event = new TelegramPublicationEvent();
        $this->assertFalse($event->timestamps);
    }

    // ─── HTTP endpoint tests ───

    /** T06: GET /admin/telegram/destinations (empty) */
    public function test_destinations_endpoint_empty(): void
    {
        $admin = $this->adminUser();

        $res = $this->actingAs($admin)
            ->getJson('/api/v1/admin/telegram/destinations');

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', []);
    }

    /** T07: GET /admin/telegram/destinations (with data) */
    public function test_destinations_endpoint_with_data(): void
    {
        $admin = $this->adminUser();
        $this->makeDestination(['name' => 'Channel A']);
        $this->makeDestination(['name' => 'Channel B']);

        $res = $this->actingAs($admin)
            ->getJson('/api/v1/admin/telegram/destinations');

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total', 2);
    }

    /** T08: GET /admin/telegram/destinations/{id} */
    public function test_show_destination(): void
    {
        $admin = $this->adminUser();
        $dest = $this->makeDestination(['name' => 'Specific']);

        $res = $this->actingAs($admin)
            ->getJson("/api/v1/admin/telegram/destinations/{$dest->id}");

        $res->assertStatus(200)
            ->assertJsonPath('data.destination.name', 'Specific')
            ->assertJsonPath('data.publication_count', 0)
            ->assertJsonPath('data.can_publish', true);
    }

    /** T09: GET /admin/telegram/destinations/{id} 404 */
    public function test_show_destination_404(): void
    {
        $admin = $this->adminUser();
        $fakeId = (string) Str::uuid();

        $res = $this->actingAs($admin)
            ->getJson("/api/v1/admin/telegram/destinations/{$fakeId}");

        $res->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    /** T10: GET /admin/telegram/publications (empty) */
    public function test_publications_endpoint_empty(): void
    {
        $admin = $this->adminUser();

        $res = $this->actingAs($admin)
            ->getJson('/api/v1/admin/telegram/publications');

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', []);
    }

    /** T11: GET /admin/telegram/publications/{id} 404 */
    public function test_show_publication_404(): void
    {
        $admin = $this->adminUser();
        $fakeId = (string) Str::uuid();

        $res = $this->actingAs($admin)
            ->getJson("/api/v1/admin/telegram/publications/{$fakeId}");

        $res->assertStatus(404);
    }

    /** T12: Unauthenticated → 401 */
    public function test_endpoints_require_auth(): void
    {
        $res = $this->getJson('/api/v1/admin/telegram/destinations');
        $res->assertStatus(401);
    }
}
