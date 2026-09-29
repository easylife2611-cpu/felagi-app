<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reporter_id')->constrained('users')->restrictOnDelete();
            $table->enum('entity_type', ['NEED', 'OFFER', 'MESSAGE', 'USER']);
            $table->uuid('entity_id');
            $table->string('reason_code', 40);
            $table->text('details')->nullable();
            $table->enum('status', ['OPEN', 'IN_REVIEW', 'RESOLVED', 'DISMISSED'])->default('OPEN');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('resolution_code', 40)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
