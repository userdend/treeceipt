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
        Schema::table('receipts', function (Blueprint $table) {
            $table->string("merchant")->nullable()->change();
            $table->decimal("total", 10, 2)->nullable()->change();
            $table->string("currency", 3)->nullable()->change();
            $table->date("receipt_date")->nullable()->change();
            $table->string("file_path")->nullable(false)->change();
            $table->json("ocr_data")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->string("merchant")->nullable(false)->change();
            $table->decimal("total", 10, 2)->nullable(false)->change();
            $table->string("currency", 3)->nullable(false)->change();
            $table->date("receipt_date")->nullable(false)->change();
            $table->string("file_path")->nullable()->change();
            $table->json("ocr_data")->nullable()->change();
        });
    }
};
