<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_creatives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('version')->default(1);
            $table->string('format', 20); // CARD|BANNER|COMPACT
            $table->string('media_asset_id', 255)->nullable();
            $table->string('copy_am_title', 255);
            $table->text('copy_am_body');
            $table->string('copy_am_cta', 80);
            $table->string('copy_am_alt', 255)->nullable();
            $table->string('copy_en_title', 255);
            $table->text('copy_en_body');
            $table->string('copy_en_cta', 80);
            $table->string('copy_en_alt', 255)->nullable();
            $table->json('validation_receipt')->nullable();
            $table->timestamps();
            $table->index('format');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_creatives');
    }
};
