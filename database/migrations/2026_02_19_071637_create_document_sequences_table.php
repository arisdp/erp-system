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
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('module_id');
            $table->uuid('company_id');

            $table->foreign('module_id')
                ->references('id')
                ->on('modules')
                ->cascadeOnDelete();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();

            $table->string('prefix'); // SO, INV, PO
            $table->integer('year');
            $table->bigInteger('current_number')->default(0);
            $table->integer('number_length')->default(4);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'module_id', 'year']);
            $table->index(['company_id', 'prefix']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
