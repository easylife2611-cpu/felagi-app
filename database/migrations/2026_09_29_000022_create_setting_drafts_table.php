<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_drafts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('setting_key', 120);
            $table->json('proposed_value_json');
            $table->foreignUuid('proposed_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['DRAFT', 'VALIDATED', 'REJECTED', 'PUBLISHED'])->default('DRAFT');
            $table->json('validation_report')->nullable();
            $table->json('impact_preview')->nullable();
            $table->timestamps();

            $table->index(['setting_key', 'status']);
            $table->index('proposed_by');

            $table->foreign('setting_key')
                  ->references('key')
                  ->on('settings')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_drafts');
    }
};
