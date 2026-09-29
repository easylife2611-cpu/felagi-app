<?php

namespace App\Services\Admin;

use App\Exceptions\ReauthRequiredException;
use App\Models\Setting;
use App\Models\User;

/**
 * Validates that a user has recently authenticated.
 *
 * The 5-minute window is LOCKED by Admin_Authorization_Contract.md §3.
 *
 * Usage:
 *   $validator = app(ReauthValidator::class);
 *   $validator->require($user, $setting);   // throws if not fresh
 *   $validator->mark($user);                // update timestamp
 */
class ReauthValidator
{
    /** Window in minutes — LOCKED by Auth Contract §3. */
    public const WINDOW_MINUTES = 5;

    /**
     * Is the user freshly authenticated within the window?
     */
    public function isFresh(User $user): bool
    {
        $lastAuth = $user->recently_authenticated_at;

        if (!$lastAuth) {
            return false;
        }

        $ageMinutes = now()->diffInMinutes($lastAuth, true);

        return $ageMinutes <= self::WINDOW_MINUTES;
    }

    /**
     * How many minutes ago was the last authentication?
     * Returns null if never authenticated.
     */
    public function ageInMinutes(User $user): ?float
    {
        $lastAuth = $user->recently_authenticated_at;

        if (!$lastAuth) {
            return null;
        }

        return (float) now()->diffInMinutes($lastAuth, true);
    }

    /**
     * Require fresh authentication for a setting change.
     *
     * @throws ReauthRequiredException
     */
    public function require(User $user, Setting $setting): void
    {
        // LOW/MEDIUM never require reauth
        if (!$setting->requiresReauth()) {
            return;
        }

        if (!$this->isFresh($user)) {
            throw new ReauthRequiredException(
                $setting->key,
                $setting->risk,
                $user->recently_authenticated_at,
            );
        }
    }

    /**
     * Mark the user as freshly authenticated (NOW).
     * Called after successful reauth flow (TOTP verify / Telegram login).
     */
    public function mark(User $user): void
    {
        $user->recently_authenticated_at = now();
        $user->save();
    }

    /**
     * Clear the reauth timestamp (used on logout or security event).
     */
    public function clear(User $user): void
    {
        $user->recently_authenticated_at = null;
        $user->save();
    }
}
