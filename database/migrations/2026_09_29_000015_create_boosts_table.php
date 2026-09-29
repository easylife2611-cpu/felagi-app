<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boosts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('need_id')->constrained('needs')->restrictOnDelete();
            $table->foreignUuid('requester_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('package_id')->constrained('boost_packages')->restrictOnDelete();
            $table->foreignUuid('payment_id')->unique()->constrained('payments')->restrictOnDelete();
            $table->decimal('price_snapshot', 12, 2);
            $table->char('currency', 3);
            $table->smallInteger('duration_days')->unsigned();
            $table->enum('status', ['PENDING', 'ACTIVE', 'EXPIRED', 'CANCELLED'])->default('PENDING');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['need_id', 'status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boosts');
    }
};
