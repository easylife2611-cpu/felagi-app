<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: `totp_recovery_codes` was declared as JSON but stored via the
 * `encrypted:array` cast, which produces a base64 string — not JSON.
 * MySQL rejects the encrypted payload under the JSON type constraint.
 *
 * Change JSON → TEXT to hold the encrypted payload.
 * Encryption-at-rest is preserved via the model cast.
 *
 * @see app/Models/User.php   casts: encrypted:array
 * @see app/Services/Admin/TotpService.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('totp_recovery_codes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('totp_recovery_codes')->nullable()->change();
        });
    }
};
