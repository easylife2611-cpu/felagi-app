<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payer_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('need_id')->constrained('needs')->restrictOnDelete();
            $table->enum('purpose', ['BOOST', 'OFFER_UNLOCK']);
            $table->string('provider', 50);
            $table->string('provider_reference', 255)->nullable();
            $table->string('idempotency_key', 100);
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('ETB');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'FAILED', 'CANCELLED', 'REVIEW_REQUIRED'])->default('PENDING');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_code', 80)->nullable();
            $table->timestamps();

            $table->unique(['payer_id', 'idempotency_key']);
            $table->unique(['provider', 'provider_reference']);
            $table->index(['status', 'created_at']);
            $table->index('need_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
