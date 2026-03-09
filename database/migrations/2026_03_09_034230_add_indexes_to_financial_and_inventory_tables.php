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
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->index(['company_id', 'account_id', 'created_at'], 'idx_journal_lines_report');
        });

        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->index(['company_id', 'product_id', 'warehouse_id'], 'idx_stocks_company_prod_wh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropIndex('idx_journal_lines_report');
        });

        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_stocks_company_prod_wh');
        });
    }
};
