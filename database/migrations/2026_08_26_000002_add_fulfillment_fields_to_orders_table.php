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
        Schema::table('orders', function (Blueprint $table) {
            // `paid_amount` is already used by the cashier payment flow.
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('shipping_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'paid_amount',
                'tracking_number',
                'shipping_note',
            ]);
        });
    }
};
