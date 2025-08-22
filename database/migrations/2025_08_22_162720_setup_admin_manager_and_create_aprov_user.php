<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, alterar admin@saga.mil.br para role 'manager'
        DB::table('users')->where('email', 'admin@saga.mil.br')->update(['role' => 'manager']);
        
        // Gerar IDT único para o usuário aprov
        do {
            $idt = 'APR' . str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
        } while (DB::table('users')->where('idt', $idt)->exists());
        
        // Criar novo usuário aprov@saga.mil.br
        DB::table('users')->insert([
            'full_name' => 'Aprovador SAGA',
            'war_name' => 'APROV',
            'email' => 'aprov@saga.mil.br',
            'password' => Hash::make('12345678'),
            'rank_id' => 1, // Soldado EV (primeiro rank disponível)
            'organization_id' => 1, // Primeira organização disponível
            'gender' => 'M',
            'ready_at_om_date' => now()->format('Y-m-d'),
            'role' => 'aprov',
            'is_active' => true,
            'idt' => $idt,
            'subunit' => '1ª Cia',
            'armed_force' => 'EB',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter admin@saga.mil.br para role 'aprov'
        DB::table('users')->where('email', 'admin@saga.mil.br')->update(['role' => 'aprov']);
        
        // Remover usuário aprov@saga.mil.br
        DB::table('users')->where('email', 'aprov@saga.mil.br')->delete();
    }
};
