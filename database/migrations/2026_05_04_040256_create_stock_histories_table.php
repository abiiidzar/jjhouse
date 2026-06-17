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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_size_id')->constrained('product_sizes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullifyOnDelete();
            $table->integer('quantity_before')->default(0);
            $table->integer('quantity_after')->default(0);
            $table->integer('quantity_changed');
            $table->string('action')->default('update'); // update, restock, sold, adjusted
            $table->text('notes')->nullable();
            $table->string('reference_type')->nullable(); // order, manual, adjustment, etc
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
            $table->index(['product_size_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
