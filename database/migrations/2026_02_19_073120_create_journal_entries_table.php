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
        Schema::create('journal_entries', function (Blueprint $table) {

            $table->uuid('id');
            $table->primary('id');

            $table->uuid('company_id');
            $table->uuid('fiscal_year_id');

            $table->string('journal_number')->unique();
            $table->date('journal_date');

            $table->text('description')->nullable();

            $table->string('reference_type')->nullable();
            $table->uuid('reference_id')->nullable(); // 🔥 ganti ke uuid

            $table->string('status')->default('draft');
            // draft, posted, cancelled

            // 🔥 UUID USER REFERENCES
            $table->uuid('created_by')->nullable();
            $table->uuid('posted_by')->nullable();

            $table->timestamp('posted_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ===== FOREIGN KEYS =====

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();

            $table->foreign('fiscal_year_id')
                ->references('id')
                ->on('fiscal_years')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('posted_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index(['company_id', 'journal_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
