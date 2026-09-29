<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparison_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comparison_id')->constrained('comparisons')->restrictOnDelete();
            $table->foreignUuid('offer_id')->constrained('offers')->restrictOnDelete();
            $table->foreignUuid('provider_id')->constrained('users')->restrictOnDelete();
            $table->json('offer_snapshot');
            $table->json('credibility_snapshot');
            $table->char('offer_snapshot_hash', 64);
            $table->timestamps();

            $table->unique(['comparison_id', 'offer_id']);
            $table->index('comparison_id');
            $table->index('provider_id');
        });

        // Composite unique (id, comparison_id) — MUST match FK reference order
        DB::statement('ALTER TABLE comparison_offers ADD UNIQUE comparison_offers_id_comparison_id_unique (id, comparison_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('comparison_offers');
    }
};
