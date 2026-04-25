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
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            return;
        }

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            // PostgreSQL: Create ENUM type if not exists, then alter column
            \Illuminate\Support\Facades\DB::statement("DO $$ BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'attendances_status_enum') THEN
                    CREATE TYPE attendances_status_enum AS ENUM ('present', 'late', 'excused', 'sick', 'absent', 'rejected');
                END IF;
            END $$");

            // Drop default, alter type, then set default back
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendances ALTER COLUMN status DROP DEFAULT");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendances ALTER COLUMN status TYPE attendances_status_enum USING status::text::attendances_status_enum");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendances ALTER COLUMN status SET DEFAULT 'absent'");
        } else {
            // MySQL: Use MODIFY COLUMN
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'late', 'excused', 'sick', 'absent', 'rejected') DEFAULT 'absent'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            return;
        }

        // CAUTION: Reverting this might fail if there are 'rejected' values in the database.
        // For PostgreSQL, we would need to drop and recreate the enum type, which is complex.
        // For now, we'll skip the revert on PostgreSQL to avoid data loss.
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            // Skip revert on PostgreSQL to avoid complex enum type recreation
            return;
        }

        // MySQL: Revert to original enum list
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'late', 'excused', 'sick', 'absent') DEFAULT 'absent'");
    }
};
