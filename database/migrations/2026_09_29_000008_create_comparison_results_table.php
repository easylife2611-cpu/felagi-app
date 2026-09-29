<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparison_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comparison_id')->constrained('comparisons')->restrictOnDelete();
            $table->uuid('comparison_offer_id');
            $table->decimal('score', 4, 2)->nullable();
            $table->json('criterion_scores');
            $table->json('strengths');
            $table->json('weaknesses');
            $table->json('missing_information');
            $table->json('risk_notes');
            $table->text('fit_explanation');
            $table->char('result_hash', 64);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['comparison_id', 'comparison_offer_id']);
            $table->index('comparison_id');
        });

        // Composite FK: result must belong to same comparison
        DB::statement(
            'ALTER TABLE comparison_results
             ADD CONSTRAINT comparison_results_offer_comparison_foreign
             FOREIGN KEY (comparison_offer_id, comparison_id)
             REFERENCES comparison_offers(id, comparison_id)
             ON DELETE RESTRICT'
        );
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE comparison_results DROP FOREIGN KEY comparison_results_offer_comparison_foreign');
        Schema::dropIfExists('comparison_results');
    }
};
