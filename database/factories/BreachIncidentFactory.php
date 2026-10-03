<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BreachIncident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreachIncidentFactory extends Factory
{
    protected $model = BreachIncident::class;

    public function definition(): array
    {
        return [
            'title'             => 'Breach ' . $this->faker->words(3, true),
            'description'       => $this->faker->paragraph(),
            'severity'          => BreachIncident::SEV_P2,
            'status'            => BreachIncident::STATUS_DETECTED,
            'data_categories'   => ['email', 'name'],
            'affected_count'    => $this->faker->numberBetween(1, 100),
            'detected_at'       => now(),
            'contained_at'      => null,
            'eca_notified_at'   => null,
            'users_notified_at' => null,
            'resolved_at'       => null,
            'reported_by'       => null,
            'remediation'       => null,
        ];
    }

    public function p1(): static
    {
        return $this->state(fn () => ['severity' => BreachIncident::SEV_P1]);
    }

    public function p2(): static
    {
        return $this->state(fn () => ['severity' => BreachIncident::SEV_P2]);
    }

    public function p3(): static
    {
        return $this->state(fn () => ['severity' => BreachIncident::SEV_P3]);
    }

    public function p4(): static
    {
        return $this->state(fn () => ['severity' => BreachIncident::SEV_P4]);
    }

    public function contained(): static
    {
        return $this->state(fn () => [
            'status'       => BreachIncident::STATUS_CONTAINED,
            'contained_at' => now(),
        ]);
    }

    public function notifiedEca(): static
    {
        return $this->state(fn () => [
            'status'          => BreachIncident::STATUS_NOTIFIED,
            'eca_notified_at' => now(),
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status'      => BreachIncident::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    public function detectedHoursAgo(int $hours): static
    {
        return $this->state(fn () => [
            'detected_at' => now()->subHours($hours),
        ]);
    }
}
