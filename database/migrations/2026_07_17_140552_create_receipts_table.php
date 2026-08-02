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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("workspace_id")->constrained("workspaces")->onDelete("cascade");
            $table->string("merchant");
            $table->decimal("total", 10, 2);
            $table->string("currency", 3)->default("MYR");
            $table->date("receipt_date");
            $table->string("file_path")->nullable();
            $table->json("ocr_data")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
