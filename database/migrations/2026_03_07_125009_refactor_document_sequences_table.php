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
        Schema::table('document_sequences', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropUnique(['company_id', 'module_id', 'year']);
            $table->dropColumn('module_id');
            
            $table->string('type')->after('company_id');
            $table->renameColumn('current_number', 'last_number');
            
            $table->unique(['company_id', 'type', 'year']);
        });
    }

    public function down(): void
    {
        Schema::table('document_sequences', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'type', 'year']);
            $table->dropColumn('type');
            $table->uuid('module_id')->nullable();
            $table->renameColumn('last_number', 'current_number');
        });
    }
};
