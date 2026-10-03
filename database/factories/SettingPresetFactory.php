<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SettingPreset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingPresetFactory extends Factory
{
    protected $model = SettingPreset::class;

    public function definition(): array
    {
        return [
            'name'         => 'preset_' . $this->faker->unique()->numerify('######'),
            'display_name' => $this->faker->words(2, true),
            'description'  => $this->faker->sentence(),
            'values_json'  => [
                'feature.payments' => false,
                'feature.boosts'   => false,
            ],
            'status'       => SettingPreset::STATUS_ACTIVE,
            'created_by'   => User::factory(),
        ];
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => SettingPreset::STATUS_ARCHIVED]);
    }

    public function withValues(array $values): static
    {
        return $this->state(fn () => ['values_json' => $values]);
    }

    public function withName(string $name): static
    {
        return $this->state(fn () => ['name' => $name]);
    }
}
