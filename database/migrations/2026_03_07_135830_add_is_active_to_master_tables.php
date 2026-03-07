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
        Schema::table('warehouses', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('name');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('name');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('name');
        });

        Schema::table('payment_terms', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('name');
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) { $table->dropColumn('is_active'); });
        Schema::table('units', function (Blueprint $table) { $table->dropColumn('is_active'); });
        Schema::table('product_categories', function (Blueprint $table) { $table->dropColumn('is_active'); });
        Schema::table('payment_terms', function (Blueprint $table) { $table->dropColumn('is_active'); });
        Schema::table('currencies', function (Blueprint $table) { $table->dropColumn('is_active'); });
    }
};
