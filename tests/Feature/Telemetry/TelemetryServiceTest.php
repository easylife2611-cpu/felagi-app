<?php

declare(strict_types=1);

namespace Tests\Feature\Telemetry;

use App\Services\Telemetry\Sinks\NullSink;
use App\Services\Telemetry\TelemetryEvent;
use App\Services\Telemetry\TelemetryService;
use App\Services\Telemetry\TelemetrySink;
use Tests\TestCase;

final class TelemetryServiceTest extends TestCase
{
    public function test_default_binding_resolves(): void
    {
        $this->assertInstanceOf(TelemetryService::class, app('telemetry'));
    }

    public function test_null_sink_discards_events(): void
    {
        $sink = new NullSink();
        $sink->write(new TelemetryEvent('x', ['a' => 1]));
        $this->assertTrue(true);
    }

    public function test_service_merges_tags(): void
    {
        $captured = [];
        $sink = new class($captured) implements TelemetrySink {
            public function __construct(private array &$bucket) {}
            public function write(TelemetryEvent $e): void { $this->bucket[] = $e; }
        };

        $svc = new TelemetryService($sink, enabled: true, tags: ['app' => 'Felagi']);
        $svc->record('user.login', ['user_id' => 42]);

        $this->assertCount(1, $captured);
        $this->assertSame('user.login', $captured[0]->name);
        $this->assertSame('Felagi', $captured[0]->properties['app']);
        $this->assertSame(42, $captured[0]->properties['user_id']);
    }

    public function test_disabled_service_does_not_write(): void
    {
        $captured = [];
        $sink = new class($captured) implements TelemetrySink {
            public function __construct(private array &$bucket) {}
            public function write(TelemetryEvent $e): void { $this->bucket[] = $e; }
        };

        $svc = new TelemetryService($sink, enabled: false, tags: []);
        $svc->record('should.not.write');

        $this->assertCount(0, $captured);
    }
}
