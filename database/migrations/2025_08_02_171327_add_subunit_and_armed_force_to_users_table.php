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
        Schema::table('users', function (Blueprint $table) {
            $table->string('subunit')->nullable()->after('organization_id')->comment('Subunidade (SU) a que pertence o usuário');
            $table->enum('armed_force', ['FAB', 'MB', 'EB'])->nullable()->after('subunit')->comment('Força Armada: FAB (Aeronáutica), MB (Marinha), EB (Exército)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subunit', 'armed_force']);
        });
    }
};
