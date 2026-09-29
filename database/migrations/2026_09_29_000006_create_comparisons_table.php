<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparisons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('need_id')->constrained('needs')->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->foreignUuid('triggered_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED'])->default('PENDING');
            $table->string('criteria_version', 50)->nullable();
            $table->string('prompt_version', 50)->nullable();
            $table->string('output_schema_version', 30)->nullable();
            $table->string('ai_provider', 50)->nullable();
            $table->string('model_id', 100)->nullable();
            $table->json('need_snapshot');
            $table->char('snapshot_hash', 64)->nullable();
            $table->unsignedInteger('eligible_offer_count')->default(0);
            $table->unsignedInteger('included_offer_count')->default(0);
            $table->unsignedInteger('input_token_count')->nullable();
            $table->unsignedInteger('output_token_count')->nullable();
            $table->decimal('estimated_cost', 12, 4)->nullable();
            $table->string('failure_code', 80)->nullable();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->unique(['need_id', 'version_number']);
            $table->index(['need_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comparisons');
    }
};
