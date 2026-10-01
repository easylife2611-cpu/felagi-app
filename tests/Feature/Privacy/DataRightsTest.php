<?php

namespace Tests\Feature\Privacy;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataRightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_user_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/v1/privacy/data');
        $response->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_update_rectifies_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->patchJson('/api/v1/privacy/data', [
            'name' => 'New Name',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_destroy_creates_erasure_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson('/api/v1/privacy/data');
        $response->assertStatus(202)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.grace_days', 7);

        $this->assertDatabaseHas('data_requests', [
            'user_id' => $user->id,
            'type'    => 'erasure',
            'status'  => 'pending',
        ]);
    }

    public function test_restrict_creates_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/privacy/restrict', [
            'scope'  => 'marketing',
            'reason' => 'No longer want marketing',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.scope', 'marketing');
    }

    public function test_export_returns_all_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/v1/privacy/export');
        $response->assertOk()
            ->assertJsonStructure(['data' => [
                'exported_at',
                'format',
                'version',
                'subject',
                'needs',
                'offers',
                'consents',
            ]]);
    }

    public function test_object_creates_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/privacy/object', [
            'purpose' => 'profiling',
            'reason'  => 'I object to profiling',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.purpose', 'profiling');
    }

    public function test_requires_auth(): void
    {
        $this->getJson('/api/v1/privacy/data')->assertUnauthorized();
        $this->patchJson('/api/v1/privacy/data', [])->assertUnauthorized();
        $this->deleteJson('/api/v1/privacy/data')->assertUnauthorized();
    }

    public function test_update_validates_email(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->patchJson('/api/v1/privacy/data', [
            'email' => 'not-an-email',
        ])->assertStatus(422);
    }
}
