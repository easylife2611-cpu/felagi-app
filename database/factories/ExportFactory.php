<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comparison;
use App\Models\Export;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExportFactory extends Factory
{
    protected $model = Export::class;

    public function definition(): array
    {
        return [
            'comparison_id' => Comparison::factory(),
            'requester_id'  => User::factory(),
            'format'        => Export::FORMAT_PDF,
            'status'        => Export::STATUS_PENDING,
            'storage_key'   => null,
            'expires_at'    => null,
            'created_at'    => now(),
            'completed_at'  => null,
        ];
    }

    public function ready(): static
    {
        return $this->state(fn () => [
            'status'       => Export::STATUS_READY,
            'storage_key'  => 'exports/' . uniqid() . '.pdf',
            'completed_at' => now(),
            'expires_at'   => now()->addDays(7),
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn () => ['status' => Export::STATUS_PROCESSING]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status'       => Export::STATUS_FAILED,
            'completed_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status'       => Export::STATUS_EXPIRED,
            'storage_key'  => 'exports/' . uniqid() . '.pdf',
            'completed_at' => now()->subDays(10),
            'expires_at'   => now()->subDay(),
        ]);
    }

    public function format(string $format): static
    {
        return $this->state(fn () => ['format' => $format]);
    }
}
