<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * WP-13b — Add reauth + TOTP 2FA columns to users table.
 *
 * - recently_authenticated_at : 5-min reauth window for HIGH/CRITICAL publish
 * - totp_secret               : encrypted TOTP secret (RFC 6238)
 * - totp_enabled_at           : when TOTP was confirmed by first code
 * - totp_recovery_codes       : encrypted JSON array of one-time recovery codes
 *
 * All columns nullable — existing users unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('recently_authenticated_at')->nullable()->after('last_login_at');
            $table->text('totp_secret')->nullable()->after('recently_authenticated_at');
            $table->timestamp('totp_enabled_at')->nullable()->after('totp_secret');
            $table->json('totp_recovery_codes')->nullable()->after('totp_enabled_at');

            $table->index('recently_authenticated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['recently_authenticated_at']);
            $table->dropColumn([
                'recently_authenticated_at',
                'totp_secret',
                'totp_enabled_at',
                'totp_recovery_codes',
            ]);
        });
    }
};
