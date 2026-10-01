<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * J (audit L276) — Dependency-aware controls.
 *
 * Adds a nullable JSON `dependencies` column to the settings table.
 * Dependency format: array of objects
 *   [
 *     { "key": "feature.payments", "value": true, "message": "..." }
 *   ]
 *
 * Additive: existing rows remain valid (NULL = no dependencies).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('dependencies')->nullable()->after('schema_json');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('dependencies');
        });
    }
};
