<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 120)->primary();
            $table->enum('group', ['FEATURE', 'CONFIG', 'CONTENT', 'SECURITY']);
            $table->enum('type', ['BOOLEAN', 'INTEGER', 'DECIMAL', 'STRING', 'JSON']);
            $table->json('value_json')->nullable();
            $table->json('default_json')->nullable();
            $table->json('schema_json')->nullable();
            $table->string('description')->nullable();
            $table->enum('risk', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL']);
            $table->boolean('is_secret')->default(false);
            $table->unsignedInteger('version_number')->default(1);
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['group', 'risk']);
            $table->index('is_secret');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
