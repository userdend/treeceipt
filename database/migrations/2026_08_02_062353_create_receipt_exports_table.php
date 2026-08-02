<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receipt_exports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained("users")
                ->cascadeOnDelete();

            $table->string('file_path')->nullable();

            $table->enum('status', [
                'processing',
                'completed',
                'failed'
            ])->default('processing');

            $table->integer('total_receipts')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_exports');
    }
};
