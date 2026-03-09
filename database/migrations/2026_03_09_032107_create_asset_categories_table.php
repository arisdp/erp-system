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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->string('name');
            $table->string('depreciation_method')->default('Straight Line');
            $table->integer('useful_life_years');
            $table->uuid('chart_of_account_id'); // Asset Account
            $table->uuid('depreciation_expense_account_id'); // Expense Account
            $table->uuid('accumulated_depreciation_account_id'); // Contra Account
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('depreciation_expense_account_id', 'dep_exp_acc_fk')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('accumulated_depreciation_account_id', 'acc_dep_acc_fk')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
