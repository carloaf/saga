# SAGA - Guia de Solução de Problemas

Este documento contém soluções para problemas comuns encontrados durante a instalação e configuração do SAGA.

## 🚨 Problemas Comuns de Instalação

### 1. Erro: "Failed to open stream: No such file or directory vendor/autoload.php"

**Causa**: O diretório `vendor/` não existe no projeto local.

**Solução**:
```bash
# Instalar dependências usando Docker
docker run --rm -v "$(pwd)":/app composer:latest install --ignore-platform-req=ext-gd --ignore-platform-req=php

# Verificar se foi criado
ls -la vendor/
```

### 2. Erro: "Permission denied" em storage/framework/views

**Causa**: Permissões incorretas nos diretórios de storage.

**Solução**:
```bash
# Corrigir permissões nos containers
docker exec saga_app_dev chown -R www-data:www-data /var/www/html/storage
docker exec saga_app_dev chmod -R 755 /var/www/html/storage
docker exec saga_app_staging chown -R www-data:www-data /var/www/html/storage
docker exec saga_app_staging chmod -R 755 /var/www/html/storage

# Limpar cache de views
docker exec saga_app_dev php artisan view:clear
docker exec saga_app_staging php artisan view:clear
```

### 3. Erro: "The bootstrap/cache directory must be present and writable"

**Causa**: Diretório de cache do Laravel não existe ou sem permissões.

**Solução**:
```bash
# Criar diretório localmente
mkdir -p bootstrap/cache

# Configurar permissões nos containers
docker exec saga_app_dev chown -R www-data:www-data /var/www/html/bootstrap/cache
docker exec saga_app_dev chmod -R 755 /var/www/html/bootstrap/cache
```

### 4. Container com status "unhealthy"

**Causa**: Aplicação retornando erro 500 devido a configuração incompleta.

**Solução**:
```bash
# Gerar chave da aplicação
docker exec saga_app_dev php artisan key:generate

# Executar migrações
docker exec saga_app_dev php artisan migrate

# Limpar e recriar caches
docker exec saga_app_dev php artisan config:clear
docker exec saga_app_dev php artisan config:cache

# Testar
curl http://localhost:8000
```

### 4.1. Container de homologação no CTA não atualiza após copiar arquivos

**Causa**: O ambiente do CTA usa bind mount em `/workspace/saga`, então copiar o arquivo não basta se o Laravel estiver com cache antigo ou se o `docker-compose` remoto não resolver o serviço `app`.

**Solução**:
```bash
# Confirmar o diretório remoto
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "ls -la /workspace/saga/"

# Limpar cache e reiniciar o container diretamente
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker exec saga_app_dev php artisan optimize:clear && docker restart saga_app_dev"

# Verificar saúde do container
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker ps | grep saga_app_dev"
```

**Observação**: se o `docker-compose` remoto falhar ao localizar o serviço `app`, prefira operar diretamente no container `saga_app_dev`.

### 5. Erro: "SQLSTATE[42P01]: Undefined table: cache"

**Causa**: Configuração de cache tentando usar banco de dados sem tabela.

**Solução**:
```bash
# Verificar se CACHE_DRIVER=redis no .env
grep CACHE_DRIVER .env

# Limpar configuração e recriar
docker exec saga_app_dev php artisan config:clear
docker exec saga_app_dev php artisan config:cache
```

## 🔧 Problemas de Docker

### 1. Erro: "permission denied while trying to connect to Docker daemon"

**Causa**: Usuário não tem permissão para acessar Docker.

**Solução**:
```bash
# Adicionar usuário ao grupo docker
sudo usermod -aG docker $USER

# Aplicar mudanças (temporário)
sudo chmod 666 /var/run/docker.sock

# Ou reiniciar sessão/sistema
```

### 2. Warning: "TARGETPLATFORM variable is not set"

**Causa**: Variáveis de build não estão definidas (normal em build local).

**Solução**: Isso é normal e não afeta o funcionamento. Os warnings podem ser ignorados.

### 3. Build falha com "ext-gd missing" ou "PHP version mismatch"

**Causa**: Composer validando requisitos de plataforma.

**Solução**:
```bash
# Usar flags para ignorar requisitos
docker run --rm -v "$(pwd)":/app composer:latest install \
  --ignore-platform-req=ext-gd \
  --ignore-platform-req=php
```

## 🚀 Procedimento de Instalação Completa

### Checklist Pós-Instalação

Execute os comandos na ordem para garantir instalação correta:

```bash
# 1. Instalar dependências
docker run --rm -v "$(pwd)":/app composer:latest install --ignore-platform-req=ext-gd --ignore-platform-req=php

# 2. Criar estrutura de diretórios
mkdir -p storage/{app/public,framework/{cache,sessions,views},logs}
mkdir -p bootstrap/cache

# 3. Configurar ambiente
cp .env.example .env
# Editar .env com suas configurações

# 4. Subir containers
docker compose up -d

# 5. Aguardar inicialização (30 segundos)
sleep 30

# 6. Configurar aplicação nos containers
docker exec saga_app_dev php artisan key:generate
docker exec saga_app_dev php artisan migrate

# 7. Configurar permissões
docker exec saga_app_dev chown -R www-data:www-data /var/www/html/storage
docker exec saga_app_dev chmod -R 755 /var/www/html/storage
docker exec saga_app_dev chown -R www-data:www-data /var/www/html/bootstrap/cache
docker exec saga_app_dev chmod -R 755 /var/www/html/bootstrap/cache

# 8. Limpar caches
docker exec saga_app_dev php artisan view:clear
docker exec saga_app_dev php artisan config:cache

# 9. Repetir para staging
docker exec saga_app_staging php artisan key:generate
docker exec saga_app_staging php artisan migrate
docker exec saga_app_staging chown -R www-data:www-data /var/www/html/storage
docker exec saga_app_staging chmod -R 755 /var/www/html/storage
docker exec saga_app_staging chown -R www-data:www-data /var/www/html/bootstrap/cache
docker exec saga_app_staging chmod -R 755 /var/www/html/bootstrap/cache
docker exec saga_app_staging php artisan view:clear
docker exec saga_app_staging php artisan config:cache

# 10. Verificar funcionamento
curl http://localhost:8000  # Deve retornar 200
curl http://localhost:8080  # Deve retornar 200
```

## 📊 Verificação de Status

### Comandos de Diagnóstico

```bash
# Status dos containers
docker ps

# Logs de erro específicos
docker logs saga_app_dev
docker logs saga_app_staging

# Teste de conectividade de banco
docker exec saga_app_dev php artisan tinker --execute="DB::connection()->getPdo();"

# Verificar configurações
docker exec saga_app_dev php artisan config:show

# Verificar permissões
docker exec saga_app_dev ls -la storage/framework/views/
docker exec saga_app_dev ls -la bootstrap/cache/
```

### Problema: `php artisan test` falha em homologação

**Causa**: O ambiente do CTA pode não ter o banco de testes configurado, por exemplo `saga_test`.

**Solução**:
```bash
# Verificar conexão de testes configurada
docker exec saga_app_dev php artisan about | grep -i database

# Alternativa: validar a funcionalidade manualmente no navegador
# após publicar a correção em homologação
```

**Ação recomendada**: provisionar um banco de testes dedicado no host antes de depender da suíte automatizada em homologação.

### Status Esperado

Após instalação completa, você deve ter:

```bash
# Containers
NAMES                STATUS                    PORTS
saga_app_dev         Up X minutes (healthy)    0.0.0.0:8000->80/tcp
saga_app_staging     Up X minutes (healthy)    0.0.0.0:8080->80/tcp
saga_db              Up X minutes              0.0.0.0:5432->5432/tcp
saga_db_staging      Up X minutes              0.0.0.0:5433->5432/tcp
saga_redis           Up X minutes              0.0.0.0:6379->6379/tcp
saga_redis_staging   Up X minutes              0.0.0.0:6380->6379/tcp

# URLs funcionais
http://localhost:8000 → HTTP 200 (Development)
http://localhost:8080 → HTTP 200 (Staging)
```

## 📞 Suporte

Se o problema persistir após seguir este guia:

1. Verifique os logs: `docker logs saga_app_dev`
2. Confirme que todos os passos foram executados
3. Reinicie os containers: `docker compose restart`
4. Em último caso, recrie tudo: `docker compose down -v && docker compose up -d`

---

**Última atualização**: 15 de agosto de 2025
