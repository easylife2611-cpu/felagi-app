<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('setting_key', 120);
            $table->unsignedInteger('version_number');
            $table->json('value_json');
            $table->foreignUuid('published_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('published_at')->useCurrent();
            $table->text('reason');
            $table->uuid('source_draft_id')->nullable();

            $table->unique(['setting_key', 'version_number']);
            $table->index('published_at');

            $table->foreign('setting_key')
                  ->references('key')
                  ->on('settings')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_versions');
    }
};
