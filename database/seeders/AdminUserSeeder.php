<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

/**
 * Grants ROLE_MAIN_ADMIN to the canonical production user.
 *
 * Idempotent — matches on user_id + role + revoked_at.
 * Does NOT create a user if missing — logs error instead.
 */
class AdminUserSeeder extends Seeder
{
    public const PRODUCTION_ADMIN_USER_ID = '01a0fbb9-a219-7094-92c7-fbcf964263be';

    public function run(): void
    {
        $user = User::find(self::PRODUCTION_ADMIN_USER_ID);

        if (!$user) {
            $this->command->error(
                "AdminUserSeeder: user " . self::PRODUCTION_ADMIN_USER_ID .
                " not found. Refusing to invent a user."
            );
            return;
        }

        $existing = UserRole::where('user_id', $user->id)
            ->where('role', UserRole::ROLE_MAIN_ADMIN)
            ->whereNull('revoked_at')
            ->first();

        if ($existing) {
            $this->command->info(
                "AdminUserSeeder: user {$user->id} already has ROLE_MAIN_ADMIN. Skipped."
            );
            return;
        }

        UserRole::create([
            'user_id'    => $user->id,
            'role'       => UserRole::ROLE_MAIN_ADMIN,
            'granted_by' => null,
            'granted_at' => now(),
            'revoked_at' => null,
        ]);

        $this->command->info(
            "AdminUserSeeder: ROLE_MAIN_ADMIN granted to {$user->id}."
        );
    }
}
