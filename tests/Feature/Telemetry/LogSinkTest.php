<?php

declare(strict_types=1);

namespace Tests\Feature\Telemetry;

use App\Services\Telemetry\Sinks\LogSink;
use App\Services\Telemetry\TelemetryEvent;
use Illuminate\Support\Facades\Log;
use Mockery;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

final class LogSinkTest extends TestCase
{
    public function test_writes_to_log_channel(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')
            ->with('[telemetry] ping', ['ok' => true])
            ->once();

        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturn($logger);

        (new LogSink('single'))->write(new TelemetryEvent('ping', ['ok' => true]));
    }
}
