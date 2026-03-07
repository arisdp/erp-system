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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('supplier_id');
            $table->uuid('purchase_order_id')->nullable();
            
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->nullOnDelete();

            $table->string('invoice_number')->unique();
            $table->string('vendor_invoice_number')->nullable();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            
            $table->string('status')->default('Draft'); // Draft, Open, Paid, Cancelled
            
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
