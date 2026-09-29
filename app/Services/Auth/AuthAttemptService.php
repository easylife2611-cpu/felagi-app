<?php

namespace App\Services\Auth;

use App\Models\AuthAttempt;
use Illuminate\Support\Str;

/**
 * AuthAttemptService — PKCE (RFC 7636) OIDC authorization-code flow.
 *
 * Per DFM §218 (LOCKED):
 *   "Telegram login uses an OIDC authorization-code flow with PKCE,
 *    state and nonce... validates signature/JWKS and
 *    issuer/audience/expiry/nonce... redirects only a single-use,
 *    short-lived handoff code."
 *
 * Timeouts (DFM §218): 15 minutes for auth attempt.
 */
class AuthAttemptService
{
    /** Auth attempt TTL (minutes). */
    public const TTL_MINUTES = 15;

    /** Handoff code TTL (seconds). */
    public const HANDOFF_TTL_SECONDS = 60;

    /** PKCE verifier length (RFC 7636: 43-128 chars). */
    public const PKCE_VERIFIER_LENGTH = 64;

    /**
     * Create a new auth attempt and return it with plaintext state/nonce/verifier.
     *
     * @return array{attempt: AuthAttempt, state: string, nonce: string, code_challenge: string, code_challenge_method: string}
     */
    public function create(string $returnUriAllowlisted): array
    {
        $state     = Str::random(64);
        $nonce     = Str::random(64);
        $verifier  = $this->generatePkceVerifier();
        $challenge = $this->generatePkceChallenge($verifier);

        $attempt = AuthAttempt::create([
            'state_hash'              => hash('sha256', $state),
            'nonce_hash'              => hash('sha256', $nonce),
            'pkce_verifier_encrypted' => $verifier,
            'handoff_hash'            => null,
            'return_uri_allowlisted'  => $returnUriAllowlisted,
            'expires_at'              => now()->addMinutes(self::TTL_MINUTES),
            'consumed_at'             => null,
            'created_at'              => now(),
        ]);

        return [
            'attempt'               => $attempt,
            'state'                 => $state,
            'nonce'                 => $nonce,
            'code_challenge'        => $challenge,
            'code_challenge_method' => 'S256',
        ];
    }

    /**
     * Find an active attempt by plaintext state.
     * Returns null if not found, expired, or consumed.
     */
    public function findByState(string $state): ?AuthAttempt
    {
        return AuthAttempt::query()
            ->where('state_hash', hash('sha256', $state))
            ->active()
            ->first();
    }

    /**
     * Validate nonce matches the attempt's stored hash.
     */
    public function validateNonce(AuthAttempt $attempt, string $nonce): bool
    {
        return hash_equals($attempt->nonce_hash, hash('sha256', $nonce));
    }

    /**
     * Get the plaintext PKCE verifier (decrypted by model cast).
     */
    public function getPkceVerifier(AuthAttempt $attempt): string
    {
        return (string) $attempt->pkce_verifier_encrypted;
    }

    /**
     * Generate a handoff code and store its hash on the attempt.
     * Returns plaintext handoff code (return once to caller).
     */
    public function generateHandoff(AuthAttempt $attempt): string
    {
        $handoff = Str::random(64);

        $attempt->handoff_hash = hash('sha256', $handoff);
        $attempt->save();

        return $handoff;
    }

    /**
     * Consume an attempt by handoff code (single-use).
     * Returns null if not found or already consumed.
     */
    public function consumeByHandoff(string $handoff): ?AuthAttempt
    {
        $attempt = AuthAttempt::query()
            ->where('handoff_hash', hash('sha256', $handoff))
            ->whereNull('consumed_at')
            ->first();

        if (!$attempt) {
            return null;
        }

        $attempt->markConsumed();

        return $attempt;
    }

    /**
     * Clean up expired attempts (called by scheduler).
     */
    public function cleanupExpired(): int
    {
        return AuthAttempt::query()
            ->where('expires_at', '<', now())
            ->whereNull('consumed_at')
            ->delete();
    }

    // ─── PKCE helpers (RFC 7636) ───

    protected function generatePkceVerifier(): string
    {
        // RFC 7636: unreserved chars [A-Z a-z 0-9 - . _ ~]
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
        $verifier = '';
        $max = strlen($alphabet) - 1;

        for ($i = 0; $i < self::PKCE_VERIFIER_LENGTH; $i++) {
            $verifier .= $alphabet[random_int(0, $max)];
        }

        return $verifier;
    }

    protected function generatePkceChallenge(string $verifier): string
    {
        // S256: BASE64URL(SHA256(ASCII(verifier)))
        $hash = hash('sha256', $verifier, true);

        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }
}
