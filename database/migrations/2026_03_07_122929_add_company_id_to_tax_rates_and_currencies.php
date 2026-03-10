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
        Schema::table('tax_rates', function (Blueprint $table) {
            $table->uuid('company_id')->nullable()->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->uuid('company_id')->nullable()->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tax_rates', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['company_id']);
            }
            $table->dropColumn('company_id');
        });

        Schema::table('currencies', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['company_id']);
            }
            $table->dropColumn('company_id');
        });
    }
};
