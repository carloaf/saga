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
        
        $hasIdtColumn = Schema::hasColumn('users', 'idt');
        $idt = null;

        // Gerar IDT único para o usuário aprov (apenas se a coluna existir)
        if ($hasIdtColumn) {
            do {
                $idt = 'APR' . str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
            } while (DB::table('users')->where('idt', $idt)->exists());
        }
        
        $rankId = DB::table('ranks')->min('id');
        $organizationId = DB::table('organizations')->min('id');

        // Criar novo usuário aprov@saga.mil.br (apenas se não existir e houver dados mínimos)
        if (
            $rankId !== null &&
            $organizationId !== null &&
            !DB::table('users')->where('email', 'aprov@saga.mil.br')->exists()
        ) {
            $userData = [
                'full_name' => 'Aprovador SAGA',
                'war_name' => 'APROV',
                'email' => 'aprov@saga.mil.br',
                'password' => Hash::make('12345678'),
                'rank_id' => $rankId,
                'organization_id' => $organizationId,
                'gender' => 'M',
                'ready_at_om_date' => now()->format('Y-m-d'),
                'role' => 'aprov',
                'is_active' => true,
                'subunit' => '1ª Cia',
                'armed_force' => 'EB',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($hasIdtColumn && $idt) {
                $userData['idt'] = $idt;
            }

            DB::table('users')->insert($userData);
        }
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
