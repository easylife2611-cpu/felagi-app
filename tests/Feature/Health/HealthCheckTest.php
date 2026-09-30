<?php

namespace Tests\Feature\Health;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_200(): void
    {
        $res = $this->getJson('/api/health');
        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'healthy');
    }

    public function test_health_reports_all_checks(): void
    {
        $res = $this->getJson('/api/health');
        $res->assertJsonStructure([
            'data' => [
                'status', 'app', 'version', 'timestamp',
                'checks' => [
                    'database' => ['status'],
                    'cache'    => ['status'],
                    'storage'  => ['status'],
                    'queue'    => ['status'],
                ],
            ],
        ]);
    }

    public function test_health_returns_request_id(): void
    {
        $res = $this->getJson('/api/health');
        $this->assertNotEmpty($res->json('request_id'));
    }
}
