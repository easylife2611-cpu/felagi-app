<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * WP-05a — auth_attempts table.
 *
 * Per DFM-FDS-1.4.md §218 (LOCKED):
 *   "Store auth_attempts: id PK, state_hash UNIQUE, nonce_hash,
 *    encrypted PKCE verifier, handoff_hash UNIQUE NULL,
 *    return_uri_allowlisted, expires_at, consumed_at NULL, created_at."
 *
 * Purpose: OIDC authorization-code flow with PKCE (RFC 7636 + RFC 6749).
 * Used by WP-27 Telegram OIDC and future OAuth providers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_attempts', function (Blueprint $table) {
            $table->id();

            $table->char('state_hash', 64)->unique();
            $table->char('nonce_hash', 64);

            // Encrypted PKCE code_verifier (RFC 7636) — Laravel encrypted cast
            $table->text('pkce_verifier_encrypted');

            // Handoff code hash (single-use, short-lived)
            $table->char('handoff_hash', 64)->nullable()->unique();

            // Return URI allowlist check
            $table->string('return_uri_allowlisted', 500);

            // Expiry + consumption
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('expires_at');
            $table->index(['consumed_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_attempts');
    }
};
