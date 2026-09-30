<?php

namespace Tests\Feature\Ads;

use App\Models\AdDelivery;
use App\Models\AdEvent;
use App\Services\Ads\AdEventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepts_valid_impression(): void
    {
        $d = AdDelivery::factory()->create();
        $svc = app(AdEventService::class);

        $res = $svc->record([
            'delivery_id' => $d->id,
            'type'        => 'IMPRESSION',
            'coverage'    => 0.8,
        ]);

        $this->assertTrue($res['accepted']);
        $this->assertSame('ACCEPTED', $res['outcome']);
        $this->assertEquals(1, AdEvent::count());
    }

    public function test_rejects_impression_with_low_coverage(): void
    {
        $d = AdDelivery::factory()->create();
        $svc = app(AdEventService::class);

        $res = $svc->record([
            'delivery_id' => $d->id,
            'type'        => 'IMPRESSION',
            'coverage'    => 0.3,
        ]);

        $this->assertFalse($res['accepted']);
        $this->assertSame('insufficient_coverage', $res['reason']);
    }

    public function test_accepts_click(): void
    {
        $d = AdDelivery::factory()->create();
        $svc = app(AdEventService::class);

        $res = $svc->record([
            'delivery_id' => $d->id,
            'type'        => 'CLICK',
        ]);

        $this->assertTrue($res['accepted']);
    }

    public function test_dedup_returns_duplicate(): void
    {
        $d = AdDelivery::factory()->create();
        $svc = app(AdEventService::class);

        $first = $svc->record([
            'event_id'    => 'evt-fixed-1',
            'delivery_id' => $d->id,
            'type'        => 'IMPRESSION',
            'coverage'    => 0.8,
        ]);

        $second = $svc->record([
            'event_id'    => 'evt-fixed-1',
            'delivery_id' => $d->id,
            'type'        => 'IMPRESSION',
            'coverage'    => 0.8,
        ]);

        $this->assertTrue($first['accepted']);
        $this->assertSame('DUPLICATE', $second['outcome']);
        $this->assertEquals(1, AdEvent::count());
    }

    public function test_rejects_invalid_type(): void
    {
        $d = AdDelivery::factory()->create();
        $svc = app(AdEventService::class);

        $res = $svc->record([
            'delivery_id' => $d->id,
            'type'        => 'INVALID_TYPE',
        ]);

        $this->assertFalse($res['accepted']);
        $this->assertSame('invalid_event_type', $res['reason']);
    }

    public function test_rejects_missing_delivery(): void
    {
        $svc = app(AdEventService::class);

        $res = $svc->record([
            'delivery_id' => '00000000-0000-0000-0000-000000000000',
            'type'        => 'CLICK',
        ]);

        $this->assertFalse($res['accepted']);
        $this->assertSame('delivery_not_found', $res['reason']);
    }

    public function test_rejects_expired_delivery(): void
    {
        $d = AdDelivery::factory()->create([
            'expires_at' => now()->subMinute(),
        ]);
        $svc = app(AdEventService::class);

        $res = $svc->record([
            'delivery_id' => $d->id,
            'type'        => 'CLICK',
        ]);

        $this->assertFalse($res['accepted']);
        $this->assertSame('delivery_expired', $res['reason']);
    }

    public function test_event_endpoint_accepts_valid_event(): void
    {
        $d = AdDelivery::factory()->create();

        $res = $this->postJson('/api/ads/events', [
            'delivery_id' => $d->id,
            'type'        => 'CLICK',
        ]);

        $res->assertStatus(202)
            ->assertJsonPath('data.outcome', 'ACCEPTED');
    }

    public function test_event_endpoint_rejects_bad_payload(): void
    {
        $res = $this->postJson('/api/ads/events', [
            'delivery_id' => 'not-a-uuid',
            'type'        => 'CLICK',
        ]);

        $res->assertStatus(422);
    }

    public function test_campaign_report_aggregates_events(): void
    {
        $d1 = AdDelivery::factory()->create();

        AdEvent::factory()->create(['delivery_id' => $d1->id, 'type' => 'IMPRESSION']);
        AdEvent::factory()->create(['delivery_id' => $d1->id, 'type' => 'IMPRESSION']);
        AdEvent::factory()->click()->create(['delivery_id' => $d1->id]);

        $svc = app(AdEventService::class);
        $report = $svc->campaignReport($d1->campaign_id);

        $this->assertEquals(2, $report['impressions']);
        $this->assertEquals(1, $report['clicks']);
        $this->assertEquals(0.5, $report['ctr']);
    }
}
