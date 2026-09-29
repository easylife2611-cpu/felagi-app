<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * WP-27 FIX: personal_access_tokens.tokenable_id must support UUID.
 *
 * Sanctum default migration uses morphs() → bigint.
 * Our User model uses UUID primary key → need uuidMorphs().
 *
 * This migration drops the old composite index, changes column type,
 * and re-adds the index.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing composite index first
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropIndex(['tokenable_type', 'tokenable_id']);
        });

        // Change tokenable_id from bigint → char(36)
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->char('tokenable_id', 36)->change();
        });

        // Re-add the index
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->index(['tokenable_type', 'tokenable_id'], 'pat_tokenable_idx');
        });
    }

    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropIndex('pat_tokenable_idx');
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('tokenable_id')->change();
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }
};
