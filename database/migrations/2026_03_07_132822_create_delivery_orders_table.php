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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('sales_order_id')->nullable();
            $table->uuid('warehouse_id');
            
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->nullOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();

            $table->string('do_number')->unique();
            $table->date('delivery_date');
            $table->string('status')->default('Draft'); // Draft, Shipped, Cancelled
            $table->string('shipped_by')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
