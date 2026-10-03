<?php

declare(strict_types=1);

namespace App\Services\Telemetry\Sinks;

use App\Services\Telemetry\TelemetryEvent;
use App\Services\Telemetry\TelemetrySink;

final class FileSink implements TelemetrySink
{
    public function __construct(
        private readonly string $path,
    ) {
    }

    public function write(TelemetryEvent $event): void
    {
        $dir = dirname($this->path);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        @file_put_contents(
            $this->path,
            json_encode($event->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX,
        );
    }
}
