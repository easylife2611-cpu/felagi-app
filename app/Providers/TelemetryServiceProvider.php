<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Telemetry\Sinks\FileSink;
use App\Services\Telemetry\Sinks\LogSink;
use App\Services\Telemetry\Sinks\NullSink;
use App\Services\Telemetry\TelemetryService;
use App\Services\Telemetry\TelemetrySink;
use Illuminate\Support\ServiceProvider;

final class TelemetryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/telemetry.php',
            'telemetry'
        );

        $this->app->singleton(TelemetrySink::class, function ($app): TelemetrySink {
            $cfg = $app['config']->get('telemetry', []);

            return match ($cfg['driver'] ?? 'null') {
                'log'   => new LogSink($cfg['drivers']['log']['channel'] ?? 'single'),
                'file'  => new FileSink($cfg['drivers']['file']['path'] ?? storage_path('logs/telemetry.log')),
                default => new NullSink(),
            };
        });

        $this->app->singleton(TelemetryService::class, function ($app): TelemetryService {
            $cfg = $app['config']->get('telemetry', []);

            return new TelemetryService(
                sink:    $app->make(TelemetrySink::class),
                enabled: (bool) ($cfg['enabled'] ?? true),
                tags:    $cfg['tags'] ?? [],
            );
        });

        $this->app->alias(TelemetryService::class, 'telemetry');
    }
}
