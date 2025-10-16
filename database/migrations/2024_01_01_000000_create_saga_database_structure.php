<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Esta migration consolidada cria toda a estrutura do banco SAGA
     * Consolidação realizada em 16/10/2025 - Estado final sincronizado com produção
     */
    public function up(): void
    {
        // 1. TABELA DE ORGANIZAÇÕES MILITARES
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('abbreviation')->nullable()->comment('Sigla da organização (ex: 11º D, 2º ELO)');
            $table->boolean('is_host')->default(false);
            $table->timestamps();
        });

        // 2. TABELA DE POSTOS E GRADUAÇÕES
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('abbreviation')->nullable()->comment('Sigla do posto/graduação (ex: 2º Ten, Cap, Maj)');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. TABELA DE USUÁRIOS
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->nullable()->unique();
            $table->string('full_name');
            $table->string('war_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar_url')->nullable();
            $table->foreignId('rank_id')->constrained('ranks');
            $table->foreignId('organization_id')->nullable()->constrained('organizations');
            $table->string('gender', 1)->nullable()->check("gender IN ('M', 'F')");
            $table->date('ready_at_om_date');
            $table->string('role')->default('user')->check("role IN ('user', 'manager', 'aprov', 'furriel', 'sgtte')");
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->string('password')->nullable();
            $table->string('subunit')->nullable()->comment('Subunidade (SU) a que pertence o usuário');
            $table->string('armed_force')->nullable()->check("armed_force IN ('FAB', 'MB', 'EB')")->comment('Força Armada: FAB (Aeronáutica), MB (Marinha), EB (Exército)');
            $table->string('status', 30)->nullable();
            $table->string('idt', 30)->unique();
        });

        // 4. TABELA DE RESERVAS DE ARRANCHAMENTO
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('booking_date');
            $table->string('meal_type')->check("meal_type IN ('breakfast', 'lunch', 'dinner')");
            $table->string('status')->default('confirmed')->check("status IN ('confirmed', 'cancelled', 'pending')");
            $table->foreignId('created_by_furriel')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_operator')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Constraint: usuário não pode ter duas reservas do mesmo tipo no mesmo dia
            $table->unique(['user_id', 'booking_date', 'meal_type']);
            
            // Índices para performance
            $table->index('created_by_furriel');
            $table->index('created_by_operator');
        });

        // 5. TABELA DE SESSÕES
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity')->index();
        });

        // 6. TABELA DE TOKENS DE RESET DE SENHA
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 7. TABELA DE CARDÁPIOS SEMANAIS
        Schema::create('weekly_menus', function (Blueprint $table) {
            $table->id();
            $table->date('week_start');
            $table->json('menu_data');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            // Índices para performance
            $table->index('week_start');
            $table->index(['week_start', 'is_active']);
        });

        // POPULAR DADOS BÁSICOS ESSENCIAIS
        $this->seedBasicData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_menus');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('users');
        Schema::dropIfExists('ranks');
        Schema::dropIfExists('organizations');
    }

    /**
     * Popular dados básicos essenciais
     */
    private function seedBasicData(): void
    {
        // ORGANIZAÇÕES - Baseado nos dados de produção
        DB::table('organizations')->insert([
            ['id' => 1, 'name' => '11º D Sup', 'abbreviation' => '11DSUP', 'is_host' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'CITEx', 'abbreviation' => 'CITEX', 'is_host' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'PMB', 'abbreviation' => 'PMB', 'is_host' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => '7º CTA', 'abbreviation' => '7CTA', 'is_host' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // POSTOS E GRADUAÇÕES - Hierarquia militar completa
        DB::table('ranks')->insert([
            // Oficiais Generais
            ['id' => 1, 'name' => 'General de Exército', 'abbreviation' => 'Gen Ex', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'General de Divisão', 'abbreviation' => 'Gen Div', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'General de Brigada', 'abbreviation' => 'Gen Bda', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            
            // Oficiais Superiores
            ['id' => 4, 'name' => 'Coronel', 'abbreviation' => 'Cel', 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Tenente-Coronel', 'abbreviation' => 'TC', 'order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Major', 'abbreviation' => 'Maj', 'order' => 6, 'created_at' => now(), 'updated_at' => now()],
            
            // Oficiais Intermediários
            ['id' => 7, 'name' => 'Capitão', 'abbreviation' => 'Cap', 'order' => 7, 'created_at' => now(), 'updated_at' => now()],
            
            // Oficiais Subalternos
            ['id' => 8, 'name' => '1º Tenente', 'abbreviation' => '1º Ten', 'order' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => '2º Tenente', 'abbreviation' => '2º Ten', 'order' => 9, 'created_at' => now(), 'updated_at' => now()],
            
            // Aspirante a Oficial
            ['id' => 10, 'name' => 'Aspirante a Oficial', 'abbreviation' => 'Asp Of', 'order' => 10, 'created_at' => now(), 'updated_at' => now()],
            
            // Subtenentes e Sargentos
            ['id' => 11, 'name' => 'Subtenente', 'abbreviation' => 'ST', 'order' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => '1º Sargento', 'abbreviation' => '1º Sgt', 'order' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => '2º Sargento', 'abbreviation' => '2º Sgt', 'order' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => '3º Sargento', 'abbreviation' => '3º Sgt', 'order' => 14, 'created_at' => now(), 'updated_at' => now()],
            
            // Cabos e Soldados
            ['id' => 15, 'name' => 'Cabo', 'abbreviation' => 'Cb', 'order' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'name' => 'Soldado EV', 'abbreviation' => 'Sd EV', 'order' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'name' => 'Soldado', 'abbreviation' => 'Sd', 'order' => 17, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // USUÁRIOS DE PRODUÇÃO - Copiados do servidor
        DB::table('users')->insert([
            [
                'id' => 1,
                'google_id' => null,
                'full_name' => 'Carlos Augusto Alves Fernandes',
                'war_name' => 'Augusto',
                'email' => 'carloafernandes@gmail.com',
                'email_verified_at' => null,
                'avatar_url' => null,
                'rank_id' => 11, // Subtenente
                'organization_id' => 1, // 11º D Sup
                'gender' => 'M',
                'ready_at_om_date' => '2025-04-28',
                'role' => 'manager',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => '2025-10-08 16:03:20',
                'updated_at' => '2025-10-08 13:09:51',
                'password' => '$2y$12$3/LGTxetp4Jx/0I67y.K8.mUUmqi5JGiLax2860cLAUJms5fo5Tfe',
                'subunit' => '1ª Cia',
                'armed_force' => 'EB',
                'status' => 'active',
                'idt' => '0334361045',
            ],
            [
                'id' => 2,
                'google_id' => null,
                'full_name' => 'Cleiton Paulo Martins',
                'war_name' => 'Martins',
                'email' => 'cleitonpaulo.martins@eb.mil.br',
                'email_verified_at' => null,
                'avatar_url' => null,
                'rank_id' => 9, // 2º Tenente
                'organization_id' => 1, // 11º D Sup
                'gender' => 'M',
                'ready_at_om_date' => '2025-01-01',
                'role' => 'manager',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => '2025-10-08 16:03:20',
                'updated_at' => '2025-10-08 16:03:20',
                'password' => '$2y$12$x9lWGUZEz9R1C6wnwJK2JupyHTRgJKLNuFBKA2j26FNMJ8SG1sJ.m',
                'subunit' => '1ª Cia',
                'armed_force' => 'EB',
                'status' => 'active',
                'idt' => '1119803177',
            ],
        ]);

        // Resetar sequences para IDs corretos
        DB::statement("SELECT setval('organizations_id_seq', (SELECT MAX(id) FROM organizations))");
        DB::statement("SELECT setval('ranks_id_seq', (SELECT MAX(id) FROM ranks))");
        DB::statement("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users))");
    }
};