<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->string('status'); // pending, paid, processing, shipped, completed, cancelled
            $table->string('payment_status'); // unpaid, dp, paid, refunded
            $table->string('payment_method')->nullable();
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->text('shipping_address');
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_size_id')->nullable()->constrained('product_sizes');
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->boolean('is_preorder')->default(false);
            $table->string('preorder_payment_type')->nullable(); // full, dp
            $table->decimal('down_payment_amount', 12, 2)->nullable();
            $table->decimal('remaining_payment', 12, 2)->nullable();
            $table->datetime('preorder_ready_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
