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
        if (DB::getDriverName() !== 'sqlite') {
            // Drop the old constraint first (não suportado em sqlite)
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_gender_check");
        }
        
        // Change the column type to varchar to allow flexibility
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender', 1)->nullable()->change();
        });
        
        // Update existing data to new format
        DB::statement("UPDATE users SET gender = 'M' WHERE gender = 'male'");
        DB::statement("UPDATE users SET gender = 'F' WHERE gender = 'female'");
        
        if (DB::getDriverName() !== 'sqlite') {
            // Add new constraint with new values
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_gender_check CHECK (gender IN ('M', 'F') OR gender IS NULL)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    // Revert the changes
        DB::statement("UPDATE users SET gender = 'male' WHERE gender = 'M'");
        DB::statement("UPDATE users SET gender = 'female' WHERE gender = 'F'");
        
        if (DB::getDriverName() !== 'sqlite') {
            // Drop the new constraint and restore the old one
            DB::statement("ALTER TABLE users DROP CONSTRAINT users_gender_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_gender_check CHECK (gender IN ('male', 'female'))");
        }
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female'])->change();
        });
    }
};
