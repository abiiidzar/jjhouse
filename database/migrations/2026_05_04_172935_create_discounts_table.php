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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'bogo', 'volume', 'flash'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable(); // untuk percentage discount
            $table->enum('applicable_to', ['all', 'categories', 'products'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->integer('priority')->default(0); // untuk multiple discounts
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
