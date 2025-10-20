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
        // Check if status column already exists
        if (!Schema::hasColumn('users', 'status')) {
            // Add status column
            Schema::table('users', function (Blueprint $table) {
                $table->string('status')->default('H')->after('is_active');
            });
        }
        
        // Drop existing CHECK constraint if it exists
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check");
        
        // Add CHECK constraint for status values (apenas active, inactive, H)
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('active', 'inactive', 'H'))");
        
        // Update existing users to have 'active' status if they are active, or 'inactive' if not
        DB::statement("UPDATE users SET status = CASE WHEN is_active = true THEN 'active' ELSE 'inactive' END WHERE status = 'H'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove CHECK constraint
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check");
        
        // Drop status column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
