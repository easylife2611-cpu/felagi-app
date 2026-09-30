<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'key'            => 'test.setting.' . Str::lower(Str::random(8)),
            'group'          => Setting::GROUP_FEATURE,
            'type'           => Setting::TYPE_BOOLEAN,
            'value_json'     => true,
            'default_json'   => false,
            'risk'           => Setting::RISK_LOW,
            'is_secret'      => false,
            'version_number' => 1,
        ];
    }

    public function highRisk(): static
    {
        return $this->state(fn () => ['risk' => Setting::RISK_HIGH]);
    }

    public function criticalRisk(): static
    {
        return $this->state(fn () => [
            'risk'      => Setting::RISK_CRITICAL,
            'is_secret' => true,
        ]);
    }

    public function inGroup(string $group): static
    {
        return $this->state(fn () => ['group' => $group]);
    }

    public function ofType(string $type): static
    {
        return $this->state(fn () => ['type' => $type]);
    }
}
