<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('needs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('requester_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->string('location_text', 255)->nullable();
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->char('currency', 3)->default('ETB');
            $table->decimal('quantity', 12, 2)->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('offer_deadline_at')->nullable();
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('OPEN');
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('telegram_publication_consent_at')->useCurrent();
            $table->unsignedInteger('telegram_publication_version')->default(1);
            $table->timestamp('telegram_publication_stopped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['category_id', 'status', 'created_at']);
            $table->index(['requester_id', 'status']);
            $table->index('offer_deadline_at');
            $table->index('location_text');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('needs');
    }
};
