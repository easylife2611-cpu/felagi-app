<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_id', 64)->unique();
            $table->foreignUuid('delivery_id')->constrained('ad_deliveries')->cascadeOnDelete();
            $table->string('type', 20); // IMPRESSION|CLICK
            $table->dateTime('observed_at');
            $table->decimal('coverage', 5, 4)->nullable();
            $table->string('dedupe_key', 128)->unique();
            $table->string('ingestion_outcome', 20)->default('ACCEPTED');
            $table->timestamps();
            $table->index('type');
            $table->index('observed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_events');
    }
};
