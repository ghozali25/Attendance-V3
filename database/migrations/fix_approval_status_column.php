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
        // Fix the approval_status column length
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('approval_status', 191)->change();
        });
        
        // Indexes are already created by performance indexes migration
        // Skip duplicate index creation
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert approval_status column length
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('approval_status')->change();
        });
        
        // Indexes are managed by performance indexes migration
        // Skip index dropping
    }
};
