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
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('workspace_users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('receipt_items', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        Schema::table('workspace_users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        Schema::table('receipt_items', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });
    }
};
