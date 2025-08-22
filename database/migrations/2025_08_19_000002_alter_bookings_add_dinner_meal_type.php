<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL: alterar enum implicará recriar constraint. Usando CHECK dynamic.
        // Em SQLite (ambiente de testes) não há suporte a DROP CONSTRAINT desta forma; simplesmente ignoramos.
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            // Em testes apenas garantir que valores existentes ainda são válidos; se por acaso tiver outro valor force para 'lunch'
            DB::table('bookings')->whereNotIn('meal_type', ['breakfast','lunch','dinner'])->update(['meal_type' => 'lunch']);
            return; // sem recriar constraint
        }

        DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_meal_type_check');
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_meal_type_check CHECK (meal_type IN ('breakfast','lunch','dinner'))");
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            // Nada a reverter em SQLite
            return;
        }
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_meal_type_check');
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_meal_type_check CHECK (meal_type IN ('breakfast','lunch'))");
    }
};
