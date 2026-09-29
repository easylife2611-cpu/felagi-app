<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boost_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->smallInteger('duration_days')->unsigned();
            $table->decimal('price', 12, 2);
            $table->char('currency', 3)->default('ETB');
            $table->boolean('active')->default(false);
            $table->uuid('published_settings_version')->nullable();
            $table->timestamps();

            $table->index(['active', 'duration_days']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boost_packages');
    }
};
