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
        // Fix reimbursements table
        Schema::table('reimbursements', function (Blueprint $table) {
            $table->string('status', 191)->change();
        });
        
        // Fix overtimes table
        Schema::table('overtimes', function (Blueprint $table) {
            $table->string('status', 191)->change();
        });
        
        // Fix cash_advances table
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->string('status', 191)->change();
        });
        
        // Fix payrolls table
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('status', 191)->change();
        });
        
        // Fix company_assets table
        Schema::table('company_assets', function (Blueprint $table) {
            $table->string('status', 191)->change();
        });
        
        // Fix company_asset_histories table
        Schema::table('company_asset_histories', function (Blueprint $table) {
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
        // Revert column lengths
        Schema::table('reimbursements', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('overtimes', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('cash_advances', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('company_assets', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('company_asset_histories', function (Blueprint $table) {
            $table->string('action')->change();
        });
        
        // Indexes are managed by scaling indexes migration
        // Skip index dropping
    }
};
