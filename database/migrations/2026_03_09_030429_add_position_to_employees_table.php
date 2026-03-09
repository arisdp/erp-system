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
        Schema::table('employees', function (Blueprint $table) {
            $table->uuid('position_id')->nullable()->after('department_id');
            $table->uuid('employment_status_id')->nullable()->after('position_id');

            $table->foreign('position_id')->references('id')->on('job_positions')->onDelete('set null');
            $table->foreign('employment_status_id')->references('id')->on('employment_statuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['employment_status_id']);
            $table->dropColumn(['position_id', 'employment_status_id']);
        });
    }
};
