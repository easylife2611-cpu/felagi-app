<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\SettingDraft;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingDraftFactory extends Factory
{
    protected $model = SettingDraft::class;

    public function definition(): array
    {
        return [
            'setting_key'         => Setting::factory(),
            'proposed_value_json' => ['enabled' => true],
            'proposed_by'         => User::factory(),
            'status'              => SettingDraft::STATUS_DRAFT,
        ];
    }

    public function validated(): static
    {
        return $this->state(fn () => [
            'status'            => SettingDraft::STATUS_VALIDATED,
            'validation_report' => ['errors' => [], 'warnings' => []],
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => SettingDraft::STATUS_PUBLISHED]);
    }
}
