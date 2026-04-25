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
        // Fix the action column length
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('action', 191)->change();
        });
        
        // Indexes are already created by scaling indexes migration
        // Skip duplicate index creation
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert action column length
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('action')->change();
        });
        
        // Indexes are managed by scaling indexes migration
        // Skip index dropping
    }
};
