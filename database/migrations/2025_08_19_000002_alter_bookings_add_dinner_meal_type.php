<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL: alterar enum implicará recriar constraint. Usando CHECK dynamic.
        // Remover constraint existente se houver (caso criada implicitamente): assumindo nome bookings_meal_type_check
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_meal_type_check');
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_meal_type_check CHECK (meal_type IN ('breakfast','lunch','dinner'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_meal_type_check');
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_meal_type_check CHECK (meal_type IN ('breakfast','lunch'))");
    }
};
