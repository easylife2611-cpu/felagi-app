<?php

declare(strict_types=1);

namespace App\Services\Telemetry;

final readonly class TelemetryEvent
{
    public function __construct(
        public string $name,
        public array $properties = [],
        public ?float $timestamp = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'properties' => $this->properties,
            'timestamp'  => $this->timestamp ?? microtime(true),
        ];
    }
}
