<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->string('actor_scope', 100);
            $table->string('scope', 100);
            $table->string('key', 100);
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->char('request_hash', 64);
            $table->unsignedSmallInteger('response_code');
            $table->json('response_body')->nullable();
            $table->enum('status', ['IN_PROGRESS', 'COMPLETE'])->default('IN_PROGRESS');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['actor_scope', 'scope', 'key']);
            $table->index('expires_at');
            $table->index('actor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
