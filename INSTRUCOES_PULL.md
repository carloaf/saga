# INSTRUÇÕES PARA APLICAR AS ÚLTIMAS ATUALIZAÇÕES DO SAGA

## 📋 Problema
Após fazer `git pull`, as modificações do código estão aplicadas, mas as alterações do banco de dados (como o novo rank "Soldado EV" e registros) não estão disponíveis.

## 🔧 Solução
Execute os comandos abaixo **NA OUTRA MÁQUINA** após fazer o pull:

### 1. Verificar Status das Migrações
```bash
cd /caminho/do/projeto/saga
docker compose exec app php artisan migrate:status
```

### 2. Executar Migrações Pendentes
```bash
docker compose exec app php artisan migrate
```

### 3. Criar Reservas de Outras Forças (se necessário)
```bash
docker compose exec app php artisan bookings:create-samples
```

### 4. Verificar se o Rank "Soldado EV" foi criado
```bash
docker compose exec app php artisan tinker --execute="
echo 'Verificando ranks disponíveis:';
\$ranks = \App\Models\Rank::orderBy('order')->get(['name', 'abbreviation', 'order']);
foreach(\$ranks as \$rank) {
    echo \$rank->order . '. ' . \$rank->name . ' (' . \$rank->abbreviation . ')' . PHP_EOL;
}
"
```

### 5. Verificar Usuários com Role Furriel (se aplicável)
```bash
docker compose exec app php artisan tinker --execute="
echo 'Verificando usuários com role furriel:';
\$furrieis = \App\Models\User::where('role', 'furriel')->get(['war_name', 'role']);
echo 'Total: ' . \$furrieis->count() . ' furriéis' . PHP_EOL;
foreach(\$furrieis as \$furriel) {
    echo '- ' . \$furriel->war_name . ' (' . \$furriel->role . ')' . PHP_EOL;
}
"
```

## 📝 O que as Migrações Fazem:

### 1. `2025_08_15_223019_update_usuario_externo_to_soldado_ev.php`
- ✅ Atualiza rank "Usuário Externo" → "Soldado EV"
- ✅ Define abreviação "Sd EV"
- ✅ Posiciona como último rank (após Soldado)

### 2. `2025_08_15_223227_add_created_by_furriel_to_bookings_table.php`
- ✅ Adiciona campo `created_by_furriel` na tabela bookings
- ✅ Permite rastrear reservas criadas por furriéis

### 3. `2025_08_15_224916_update_users_role_constraint_add_furriel.php`
- ✅ Atualiza constraint para aceitar role 'furriel'
- ✅ Permite criar usuários com papel de furriel

## 🚨 Importante:
- **SEMPRE** executar `docker compose exec app php artisan migrate` após pull
- **NÃO** executar seeders em produção, apenas migrações
- Verificar se os containers estão rodando: `docker compose ps`

## 🎯 Resultado Esperado:
Após executar as migrações, você deve ter:
- ✅ Rank "Soldado EV" disponível na lista
- ✅ Possibilidade de criar usuários com role 'furriel'
- ✅ Reservas de outras forças armadas visíveis no dashboard
- ✅ Todas as funcionalidades do sistema furriel operacionais
