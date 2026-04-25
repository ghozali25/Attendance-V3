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
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['job_title_id']);
            // Rename column
            $table->renameColumn('job_title_id', 'job_level_id');
            // Add new foreign key constraint to job_levels table
            $table->foreign('job_level_id')->references('id')->on('job_levels')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['job_level_id']);
            // Rename column back
            $table->renameColumn('job_level_id', 'job_title_id');
            // Add back foreign key constraint to job_titles table
            $table->foreign('job_title_id')->references('id')->on('job_titles')->nullOnDelete();
        });
    }
};
