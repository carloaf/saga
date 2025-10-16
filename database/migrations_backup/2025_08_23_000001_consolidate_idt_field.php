<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Consolidação completa do campo IDT
     * 
     * Esta migration consolida as seguintes migrations:
     * - 2025_08_21_120000_add_idt_to_users_table.php
     * - 2025_08_21_130000_make_idt_unique_not_nullable_on_users_table.php
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adicionar coluna IDT como not null e unique em uma única operação
            if (!Schema::hasColumn('users', 'idt')) {
                $table->string('idt', 30)->unique()->after('google_id');
            }
        });
        
        // Preencher IDT para usuários existentes que não tenham (precaução)
        $usersWithoutIdt = DB::table('users')->whereNull('idt')->orWhere('idt', '')->get();
        foreach ($usersWithoutIdt as $user) {
            do {
                $idt = 'USR' . str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
            } while (DB::table('users')->where('idt', $idt)->exists());
            
            DB::table('users')->where('id', $user->id)->update(['idt' => $idt]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'idt')) {
                $table->dropColumn('idt');
            }
        });
    }
};
