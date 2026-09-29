<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;

/**
 * Setting authorization per Admin_Authorization_Contract.md §1.
 *
 * Roles:
 *   - MAIN_ADMIN   : full access to all settings (except payment truth edit)
 *   - ADMIN        : read + edit non-secret, LOW/MEDIUM risk
 *   - MODERATOR    : read non-secret only
 *
 * HIGH/CRITICAL publish requires MAIN_ADMIN (reauth deferred to WP-13b).
 */
class SettingPolicy
{
    /** View: any active admin role, but secret requires MAIN_ADMIN. */
    public function view(User $user, Setting $setting): bool
    {
        if ($this->isMainAdmin($user)) {
            return true;
        }

        if ($setting->is_secret) {
            return false;
        }

        return $this->hasAnyAdminRole($user);
    }

    /** Create draft: ADMIN or MAIN_ADMIN. */
    public function create(User $user): bool
    {
        return $this->hasAnyAdminRole($user);
    }

    /** Update draft: secret → MAIN_ADMIN only; else ADMIN+. */
    public function update(User $user, Setting $setting): bool
    {
        if ($setting->is_secret && !$this->isMainAdmin($user)) {
            return false;
        }

        return $this->hasAnyAdminRole($user);
    }

    /**
     * Publish:
     *   - LOW/MEDIUM : ADMIN or MAIN_ADMIN
     *   - HIGH       : MAIN_ADMIN only
     *   - CRITICAL   : MAIN_ADMIN only (second factor deferred to WP-13b)
     */
    public function publish(User $user, Setting $setting): bool
    {
        if ($setting->requiresReauth()) {
            return $this->isMainAdmin($user);
        }

        return $this->hasAnyAdminRole($user);
    }

    /** Rollback = publish a new version → same as publish. */
    public function rollback(User $user, Setting $setting): bool
    {
        return $this->publish($user, $setting);
    }

    // ─── Helpers ───

    protected function isMainAdmin(User $user): bool
    {
        return $user->roles()
            ->where('role', UserRole::ROLE_MAIN_ADMIN)
            ->whereNull('revoked_at')
            ->exists();
    }

    protected function hasAnyAdminRole(User $user): bool
    {
        return $user->roles()
            ->whereIn('role', [UserRole::ROLE_MAIN_ADMIN, UserRole::ROLE_ADMIN])
            ->whereNull('revoked_at')
            ->exists();
    }
}
