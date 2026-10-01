<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparison_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comparison_id')->constrained('comparisons')->cascadeOnDelete();
            $table->foreignUuid('provider_id')->constrained('users')->restrictOnDelete();
            $table->string('rating', 20);
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('SUBMITTED');
            $table->timestamps();

            $table->unique(['comparison_id', 'provider_id']);
            $table->index(['provider_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comparison_feedback');
    }
};
