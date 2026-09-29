<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('role', ['MAIN_ADMIN', 'ADMIN', 'MODERATOR']);
            $table->foreignUuid('granted_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('revoked_at')->nullable();

            $table->primary(['user_id', 'role']);
            $table->index(['role', 'revoked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
