<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Fase 2: Após backfill garantir UNIQUE + NOT NULL
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'idt')) {
                // Normalizar espaços/brancos antes de aplicar constraints
            }
        });

        // Remover registros duplicados (mantendo o menor id) antes de unique (precaução)
        DB::statement(<<<SQL
            DELETE FROM users u
            USING users u2
            WHERE u.id > u2.id
              AND u.idt IS NOT NULL
              AND u2.idt IS NOT NULL
              AND u.idt = u2.idt;
        SQL);

        // Preencher IDT vazio com placeholder controlado (se ainda houver)
        DB::statement("UPDATE users SET idt = CONCAT('PENDENTE_', id) WHERE idt IS NULL OR TRIM(idt) = ''");

        Schema::table('users', function (Blueprint $table) {
            $table->string('idt', 30)->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('idt');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'idt')) {
                $table->dropUnique(['idt']);
                $table->string('idt', 30)->nullable()->change();
            }
        });
    }
};
