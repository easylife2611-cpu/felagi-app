<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparison_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comparison_id')->constrained('comparisons')->restrictOnDelete();
            $table->unsignedInteger('attempt_number');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->enum('status', ['PROCESSING', 'SUCCEEDED', 'FAILED'])->default('PROCESSING');
            $table->string('provider_request_id', 150)->nullable();
            $table->string('failure_code', 80)->nullable();
            $table->json('token_usage')->nullable();

            $table->unique(['comparison_id', 'attempt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comparison_attempts');
    }
};
