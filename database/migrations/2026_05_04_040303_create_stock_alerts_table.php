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
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_size_id')->constrained('product_sizes')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('threshold')->default(5); // Alert ketika stock di bawah nilai ini
            $table->integer('current_stock');
            $table->string('status')->default('active'); // active, acknowledged, resolved
            $table->timestamp('first_triggered_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullifyOnDelete();
            $table->text('acknowledgment_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->integer('restock_quantity')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'product_id']);
            $table->index(['first_triggered_at']);
            $table->unique(['product_size_id', 'status'], 'unique_active_alert_per_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
    }
};
