<?php

namespace App\Http\Middleware;

use App\Models\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the authenticated user has at least one active admin role.
 * Used by all /admin/* web routes.
 *
 * Roles (canonical — from Admin_Authorization_Contract.md):
 *   MAIN_ADMIN  full access
 *   ADMIN       scoped access
 *   MODERATOR   reports + telegram only
 */
class EnsureAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest('/admin/login');
        }

        $hasRole = $user->roles()
            ->whereIn('role', [
                UserRole::ROLE_MAIN_ADMIN,
                UserRole::ROLE_ADMIN,
                UserRole::ROLE_MODERATOR,
            ])
            ->whereNull('revoked_at')
            ->exists();

        if (! $hasRole) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
