<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boosts', function (Blueprint $table) {
            // Drop FK + unique first
            $table->dropForeign(['payment_id']);
            $table->dropUnique(['payment_id']);
        });

        Schema::table('boosts', function (Blueprint $table) {
            $table->uuid('payment_id')->nullable()->change();
        });

        Schema::table('boosts', function (Blueprint $table) {
            $table->unique('payment_id');
            $table->foreign('payment_id')->references('id')->on('payments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('boosts', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropUnique(['payment_id']);
        });

        Schema::table('boosts', function (Blueprint $table) {
            $table->uuid('payment_id')->nullable(false)->change();
        });

        Schema::table('boosts', function (Blueprint $table) {
            $table->unique('payment_id');
            $table->foreign('payment_id')->references('id')->on('payments')->restrictOnDelete();
        });
    }
};
