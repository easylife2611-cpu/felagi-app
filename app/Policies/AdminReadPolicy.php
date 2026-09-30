<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRole;

/**
 * Admin read authorization per Admin_Authorization_Contract.md.
 *
 * Capability model: admin.view.<area>
 *
 * Role grants (from contract):
 *   MAIN_ADMIN : full read (all areas incl. secrets)
 *   ADMIN      : read all non-secret areas
 *   MODERATOR  : reports + telegram only
 *
 * Deny by default.
 */
class AdminReadPolicy
{
    /** Areas that require MAIN_ADMIN (secret-related). */
    private const SECRET_AREAS = ['security'];

    /** Areas a MODERATOR may read. */
    private const MODERATOR_AREAS = ['reports', 'telegram'];

    /**
     * Read access to a given admin area.
     *
     * @param  string  $area  semantic area key (e.g. 'users', 'audit', 'security')
     */
    public function viewAny(User $user, string $area): bool
    {
        if (! $this->hasAdminAccess($user)) {
            return false;
        }

        if ($this->isMainAdmin($user)) {
            return true;
        }

        if ($this->isModerator($user)) {
            return in_array($area, self::MODERATOR_AREAS, true);
        }

        // ADMIN
        return ! in_array($area, self::SECRET_AREAS, true);
    }

    // ─── Helpers ───

    protected function isMainAdmin(User $user): bool
    {
        return $user->roles()
            ->where('role', UserRole::ROLE_MAIN_ADMIN)
            ->whereNull('revoked_at')
            ->exists();
    }

    protected function isModerator(User $user): bool
    {
        // MODERATOR-only check (no ADMIN/MAIN_ADMIN role)
        $hasModerator = $user->roles()
            ->where('role', UserRole::ROLE_MODERATOR)
            ->whereNull('revoked_at')
            ->exists();

        $hasHigher = $user->roles()
            ->whereIn('role', [UserRole::ROLE_MAIN_ADMIN, UserRole::ROLE_ADMIN])
            ->whereNull('revoked_at')
            ->exists();

        return $hasModerator && ! $hasHigher;
    }

    protected function hasAdminAccess(User $user): bool
    {
        return $user->roles()
            ->whereIn('role', [
                UserRole::ROLE_MAIN_ADMIN,
                UserRole::ROLE_ADMIN,
                UserRole::ROLE_MODERATOR,
            ])
            ->whereNull('revoked_at')
            ->exists();
    }
}
