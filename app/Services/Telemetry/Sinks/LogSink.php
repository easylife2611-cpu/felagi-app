<?php

declare(strict_types=1);

namespace App\Services\Telemetry\Sinks;

use App\Services\Telemetry\TelemetryEvent;
use App\Services\Telemetry\TelemetrySink;
use Illuminate\Support\Facades\Log;

final class LogSink implements TelemetrySink
{
    public function __construct(
        private readonly string $channel = 'single',
    ) {
    }

    public function write(TelemetryEvent $event): void
    {
        Log::channel($this->channel)->info('[telemetry] ' . $event->name, $event->properties);
    }
}
