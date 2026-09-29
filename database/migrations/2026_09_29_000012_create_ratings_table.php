<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('need_id')->constrained('needs')->restrictOnDelete();
            $table->foreignUuid('from_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('to_user_id')->constrained('users')->restrictOnDelete();
            $table->tinyInteger('score')->unsigned();
            $table->text('review')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['need_id', 'from_user_id', 'to_user_id']);
        });

        // Check constraints
        DB::statement('ALTER TABLE ratings ADD CONSTRAINT ratings_score_check CHECK (score BETWEEN 1 AND 5)');
        DB::statement('ALTER TABLE ratings ADD CONSTRAINT ratings_no_self_check CHECK (from_user_id <> to_user_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
