<?php

declare(strict_types=1);

namespace App\Services\Telemetry;

interface TelemetrySink
{
    public function write(TelemetryEvent $event): void;
}
