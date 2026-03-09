<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration is a no-op if company_id already exists (added during table creation).
     */
    public function up(): void
    {
        // PostgreSQL-safe: only add if it doesn't already exist
        if (!Schema::hasColumn('payment_terms', 'company_id')) {
            Schema::table('payment_terms', function (Blueprint $table) {
                $table->uuid('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payment_terms', 'company_id')) {
            Schema::table('payment_terms', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};
