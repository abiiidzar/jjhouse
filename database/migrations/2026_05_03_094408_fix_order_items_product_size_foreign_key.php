<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['product_size_id']);
            // Re-create it with onDelete('set null')
            $table->foreign('product_size_id')
                ->references('id')
                ->on('product_sizes')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['product_size_id']);
            // Re-create the original constraint without onDelete
            $table->foreign('product_size_id')
                ->references('id')
                ->on('product_sizes');
        });
    }
};
