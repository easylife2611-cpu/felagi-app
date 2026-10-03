<?php

declare(strict_types=1);

namespace App\Services\Telemetry\Sinks;

use App\Services\Telemetry\TelemetryEvent;
use App\Services\Telemetry\TelemetrySink;

final class NullSink implements TelemetrySink
{
    public function write(TelemetryEvent $event): void
    {
        // no-op
    }
}
