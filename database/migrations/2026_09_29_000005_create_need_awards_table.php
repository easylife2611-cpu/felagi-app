<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('need_awards', function (Blueprint $table) {
            $table->foreignUuid('need_id')->primary()->constrained('needs')->restrictOnDelete();
            $table->uuid('offer_id')->unique();
            $table->foreignUuid('accepted_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('accepted_at');
            $table->string('request_id', 100);
        });

        // Composite FK: award cannot point to another Need's Offer
        DB::statement(
            'ALTER TABLE need_awards 
             ADD CONSTRAINT need_awards_offer_id_need_id_foreign 
             FOREIGN KEY (offer_id, need_id) 
             REFERENCES offers(id, need_id) 
             ON DELETE RESTRICT'
        );
    }

    public function down(): void
    {
        // Drop composite FK first
        DB::statement('ALTER TABLE need_awards DROP FOREIGN KEY need_awards_offer_id_need_id_foreign');
        Schema::dropIfExists('need_awards');
    }
};
