<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Fase 1: adiciona coluna IDT como nullable (sem unique) para permitir backfill em produção
            if (!Schema::hasColumn('users', 'idt')) {
                $table->string('idt', 30)->nullable()->after('google_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'idt')) {
                $table->dropColumn('idt');
            }
        });
    }
};
