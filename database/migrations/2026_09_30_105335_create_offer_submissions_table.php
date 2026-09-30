<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('offer_submissions')) {
            return;
        }
        Schema::create('offer_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('need_id');
            $table->uuid('provider_id');
            $table->string('status', 32)->default('PENDING_PAYMENT');
            $table->uuid('payment_id')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['need_id', 'provider_id']);
            $table->index('status');

            $table->foreign('need_id')->references('id')->on('needs')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_submissions');
    }
};
