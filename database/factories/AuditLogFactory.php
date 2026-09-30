<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        $occurredAt = now();
        $prevHash = hash('sha256', Str::uuid()->toString());
        $hash = hash('sha256', $prevHash . $occurredAt->toIso8601String());

        return [
            'actor_id' => User::factory(),
            'action' => 'setting.changed',
            'entity_type' => 'setting',
            'entity_id' => (string) Str::uuid(),
            'request_id' => (string) Str::uuid(),
            'reason' => $this->faker->sentence(),
            'before_digest' => hash('sha256', 'before-' . Str::uuid()),
            'after_digest' => hash('sha256', 'after-' . Str::uuid()),
            'safe_metadata' => ['source' => 'factory'],
            'occurred_at' => $occurredAt,
            'prev_hash' => $prevHash,
            'hash' => $hash,
        ];
    }
}
