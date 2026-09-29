<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('need_id')->nullable()->constrained('needs')->restrictOnDelete();
            $table->foreignUuid('offer_id')->nullable()->constrained('offers')->restrictOnDelete();
            $table->foreignUuid('message_id')->nullable()->constrained('messages')->restrictOnDelete();
            $table->enum('purpose', ['PROFILE', 'NEED', 'OFFER', 'MESSAGE']);
            $table->string('storage_disk');
            $table->string('storage_key');
            $table->string('original_name');
            $table->string('detected_mime');
            $table->unsignedBigInteger('byte_size');
            $table->char('sha256', 64);
            $table->enum('visibility', ['PUBLIC', 'PRIVATE'])->default('PRIVATE');
            $table->enum('scan_status', ['PENDING', 'CLEAN', 'REJECTED'])->default('PENDING');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->index('uploaded_by');
            $table->index('need_id');
            $table->index('offer_id');
            $table->index('message_id');
            $table->index('scan_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
