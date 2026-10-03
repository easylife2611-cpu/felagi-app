<?php

declare(strict_types=1);

namespace App\Services\Telemetry;

final class TelemetryService
{
    public function __construct(
        private readonly TelemetrySink $sink,
        private readonly bool $enabled = true,
        private readonly array $tags = [],
    ) {
    }

    public function record(string $name, array $properties = []): void
    {
        if (! $this->enabled) {
            return;
        }

        $this->sink->write(new TelemetryEvent(
            name: $name,
            properties: array_merge($this->tags, $properties),
        ));
    }

    public function event(string $name, array $properties = []): void
    {
        $this->record($name, $properties);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
