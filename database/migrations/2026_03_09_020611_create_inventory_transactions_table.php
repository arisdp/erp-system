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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('warehouse_id');
            $table->uuid('product_id');
            
            $table->string('transaction_type'); // 'GRN', 'DO', 'Transfer In', 'Transfer Out', 'Adjustment', 'Opening Balance'
            
            $table->string('reference_type')->nullable(); // Polymorphic type
            $table->uuid('reference_id')->nullable(); // Polymorphic ID
            
            $table->decimal('quantity', 15, 6); // Positive or negative
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->dateTime('transaction_date');
            $table->text('notes')->nullable();

            // Setup foreign keys manually initially if you don't use constrained()
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
