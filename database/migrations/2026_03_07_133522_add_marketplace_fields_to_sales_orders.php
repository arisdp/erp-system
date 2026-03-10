<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->uuid('marketplace_id')->nullable()->after('customer_id');
            $table->foreign('marketplace_id')->references('id')->on('marketplaces')->nullOnDelete();
            
            $table->string('transaction_type')->default('Offline')->after('so_number'); // Offline, Online
            
            $table->decimal('platform_fee', 18, 2)->default(0)->after('net_amount');
            $table->decimal('platform_discount', 18, 2)->default(0)->after('platform_fee');
            $table->decimal('platform_voucher', 18, 2)->default(0)->after('platform_discount');
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['marketplace_id']);
            }
            $table->dropColumn(['marketplace_id', 'transaction_type', 'platform_fee', 'platform_discount', 'platform_voucher']);
        });
    }
};
