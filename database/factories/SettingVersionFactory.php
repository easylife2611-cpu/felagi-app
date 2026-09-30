<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\SettingVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingVersionFactory extends Factory
{
    protected $model = SettingVersion::class;

    public function definition(): array
    {
        return [
            // FK → settings.key — create parent Setting first
            'setting_key' => function () {
                return Setting::factory()->create()->key;
            },
            'version_number' => $this->faker->numberBetween(1, 100),
            'value_json' => ['enabled' => $this->faker->boolean()],
            'published_by' => User::factory(),
            'published_at' => now(),
            'reason' => $this->faker->sentence(),
            'source_draft_id' => null,
        ];
    }

    /** Attach to an existing Setting key */
    public function forSetting(string $key): static
    {
        return $this->state(fn () => ['setting_key' => $key]);
    }
}
