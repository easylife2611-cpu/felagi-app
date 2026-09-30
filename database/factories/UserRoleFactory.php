<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRoleFactory extends Factory
{
    protected $model = UserRole::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'role'       => UserRole::ROLE_ADMIN,
            'granted_by' => null,
        ];
    }

    public function mainAdmin(): static
    {
        return $this->state(fn () => ['role' => UserRole::ROLE_MAIN_ADMIN]);
    }

    public function moderator(): static
    {
        return $this->state(fn () => ['role' => UserRole::ROLE_MODERATOR]);
    }

    public function revoked(): static
    {
        return $this->state(fn () => ['revoked_at' => now()]);
    }
}
