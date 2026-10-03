<?php

declare(strict_types=1);

namespace Tests\Feature\Telemetry;

use App\Services\Telemetry\Sinks\FileSink;
use App\Services\Telemetry\TelemetryEvent;
use Tests\TestCase;

final class FileSinkTest extends TestCase
{
    public function test_writes_json_lines(): void
    {
        $path = storage_path('framework/testing/telemetry-' . uniqid() . '.log');
        @unlink($path);

        $sink = new FileSink($path);
        $sink->write(new TelemetryEvent('a', ['x' => 1]));
        $sink->write(new TelemetryEvent('b', ['y' => 2]));

        $lines = array_values(array_filter(explode(PHP_EOL, (string) file_get_contents($path))));
        $this->assertCount(2, $lines);

        $first = json_decode($lines[0], true);
        $this->assertSame('a', $first['name']);
        $this->assertSame(1, $first['properties']['x']);

        @unlink($path);
    }
}
