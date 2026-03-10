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
        if (DB::getDriverName() === 'sqlite') {
            Schema::dropIfExists('document_sequences');
            Schema::create('document_sequences', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('company_id');
                $table->string('type');
                $table->string('prefix')->nullable();
                $table->integer('year');
                $table->bigInteger('last_number')->default(0);
                $table->integer('number_length')->default(4);
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['company_id', 'type', 'year']);
                $table->index(['company_id', 'type']);
            });
            return;
        }

        Schema::table('document_sequences', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['module_id']);
            }
            $table->dropUnique(['company_id', 'module_id', 'year']);
            $table->dropColumn('module_id');
            
            $table->string('type')->after('company_id');
            $table->renameColumn('current_number', 'last_number');
            
            $table->unique(['company_id', 'type', 'year']);
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::dropIfExists('document_sequences');
            // Re-create it with original schema if needed, but for tests discard is fine
            return;
        }

        Schema::table('document_sequences', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'type', 'year']);
            $table->dropColumn('type');
            $table->uuid('module_id')->nullable();
            $table->renameColumn('last_number', 'current_number');
        });
    }
};
