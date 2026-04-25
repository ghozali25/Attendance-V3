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
        // Fix the group column length
        Schema::table('settings', function (Blueprint $table) {
            $table->string('group', 191)->change();
        });
        
        // Index is already created by add_group_index_to_settings_table migration
        // Skip duplicate index creation
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert group column length
        Schema::table('settings', function (Blueprint $table) {
            $table->string('group')->change();
        });
        
        // Index is managed by add_group_index_to_settings_table migration
        // Skip index dropping
    }
};
