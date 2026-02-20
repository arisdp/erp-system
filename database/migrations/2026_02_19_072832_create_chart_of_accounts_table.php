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
        Schema::create('chart_of_accounts', function (Blueprint $table) {

            $table->uuid('id');
            $table->primary('id'); // 🔥 penting untuk PostgreSQL

            $table->uuid('company_id');
            $table->uuid('account_type_id');
            $table->uuid('account_group_id')->nullable();
            $table->uuid('parent_id')->nullable();

            $table->string('account_code');
            $table->string('account_name');

            $table->boolean('is_postable')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // ===== FOREIGN KEYS DI BAWAH =====

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();

            $table->foreign('account_type_id')
                ->references('id')
                ->on('account_types')
                ->cascadeOnDelete();

            $table->foreign('account_group_id')
                ->references('id')
                ->on('account_groups')
                ->nullOnDelete();

            // 🔥 SELF REFERENCE FIX
            $table->foreign('parent_id')
                ->references('id')
                ->on('chart_of_accounts')
                ->nullOnDelete(); // lebih aman daripada cascade

            // ===== INDEX & UNIQUE =====

            $table->unique(['company_id', 'account_code']);
            $table->index(['company_id', 'account_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
