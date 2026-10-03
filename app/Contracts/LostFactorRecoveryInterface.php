<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Lost-Factor Recovery — contract marker (L346-D).
 *
 * ─────────────────────────────────────────────────────────────
 *  INTENTIONALLY EMPTY
 * ─────────────────────────────────────────────────────────────
 *
 * This interface exists as a naming anchor for REQ-WP13C-004 /
 * GAP-53. It declares NO methods because the recovery workflow is
 * not yet defined by the design owner.
 *
 * Adding method signatures now would violate the project
 * constitution:
 *   - "Do not guess missing requirements"
 *   - "UNKNOWN != MISSING"
 *
 * The 6 open questions are catalogued in:
 *   docs/spec-requests/REQ-WP13C-004_lost_factor_recovery.md
 *
 * Design context (LOCKED):
 *   - Auth Contract §449: "lost-factor recovery is a controlled,
 *     audited process, not a secret bypass."
 *
 * When the design owner approves a spec, method signatures will be
 * added here and a concrete implementation will bind via
 * AppServiceProvider or a dedicated service provider.
 *
 * Related:
 *   - docs/reports/GAP-53_DESIGN_PROPOSAL_20261002.md
 *   - docs/spec-requests/REQ-WP13C-004_lost_factor_recovery.md
 *
 * @see \App\Services\Auth\TwoFactorService  (existing factor core)
 * @see \App\Services\Auth\ReauthValidator  (5-min reauth window)
 */
interface LostFactorRecoveryInterface
{
    /**
     * Marker method.
     *
     * Returns the GAP reference so any caller that accidentally
     * resolves this interface gets a clear signal that the flow is
     * unimplemented. This is deliberately non-operational.
     *
     * @return string  Always returns 'GAP-53'.
     */
    public static function gapReference(): string;
}
