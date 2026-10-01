<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->string('profile_photo_url')->nullable()->change();
            $table->integer('version')->default(1)->change();
            $table->string('status')->default('ACTIVE')->change();
        });
    }

    public function down(): void
    {
        // no-op
    }
};
