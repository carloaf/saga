# SAGA - Quick Reference: Desenvolvimento Multi-Arquitetura
# Referência rápida para desenvolvedores

## 🚀 COMANDOS ESSENCIAIS

### Desenvolvimento Diário
```bash
# Iniciar desenvolvimento
git checkout dev && git pull origin dev
git checkout -b feature/minha-funcionalidade
docker compose up -d

# Verificar status
curl http://localhost:8000  # Dev (deve retornar 200)
curl http://localhost:8080  # Staging (deve retornar 200)

# Trabalhar normalmente (hot reload ativo)
# Fazer commits frequentes
git add . && git commit -m "feat: nova funcionalidade"
git push origin feature/minha-funcionalidade
```

### Ambientes Ativos
```
DESENVOLVIMENTO: http://localhost:8000 (.env)
STAGING:        http://localhost:8080 (.env.staging)  
PRODUÇÃO:       porta 80 (.env.production)
```

### Estrutura de Branches
```
main (produção) ←── Pull Request ←── dev (staging) ←── feature/nova-funcionalidade
```

## 📦 IMAGENS E CONTAINERS

### Desenvolvimento (saga/app:dev)
- **Mount**: Código fonte live (`./:/var/www/html`)
- **Configuração**: `.env` (DB: saga, senha: saga_password)
- **Hot Reload**: ✅ Ativado
- **Debug**: ✅ Ativado
- **Uso**: Desenvolvimento diário

### Staging (saga/app:staging)
- **Mount**: Código + `.env.staging` específico
- **Configuração**: `.env.staging` (DB: saga_staging, senha: saga_password_staging)
- **Testing**: ✅ Ambiente isolado para QA
- **Debug**: ✅ Ativado para troubleshooting
- **Uso**: Homologação e testes

### Produção (saga/app:production)
- **Mount**: ❌ Código embedado na imagem
- **Configuração**: `.env.production`
- **Performance**: ✅ Otimizado
- **Debug**: ❌ Desabilitado
- **Uso**: Ambiente live

## 🔧 TROUBLESHOOTING RÁPIDO

### Container Unhealthy
```bash
# Verificar logs
docker logs saga_app_dev --tail 50

# Problema comum: credenciais de banco
# Verificar .env vs docker-compose.yml
docker exec saga_app_dev cat .env | grep DB_

# Reiniciar se necessário
docker compose restart app
```

### Erro 500
```bash
# Logs do Laravel
docker exec saga_app_dev tail storage/logs/laravel.log

# Limpar cache
docker exec saga_app_dev php artisan cache:clear
docker exec saga_app_dev php artisan config:clear

# Verificar migrations
docker exec saga_app_dev php artisan migrate:status
```

### Build Issues
```bash
# Rebuild completo
docker compose build --no-cache

# Build multi-arch
./scripts/deployment/build-multiarch.sh

# Verificar dependências
docker exec saga_app_dev composer install
docker exec saga_app_dev npm install
```

## 📋 CHECKLIST PRE-DEPLOY

### Antes do Merge para dev
- [ ] Testes passando localmente
- [ ] Código funcionando em http://localhost:8000
- [ ] Commit messages seguem padrão
- [ ] Sem credenciais hardcoded

### Antes do Deploy para Staging  
- [ ] Merge para dev realizado
- [ ] Staging funcionando em http://localhost:8080
- [ ] Testes de integração OK
- [ ] QA validou funcionalidades

### Antes do Deploy para Produção
- [ ] Staging 100% validado
- [ ] Backup de produção realizado
- [ ] Script de rollback preparado
- [ ] Monitoramento ativo

## 🎯 VANTAGENS RESUMIDAS

✅ **3 ambientes isolados** com bancos e configurações separadas
✅ **Hot reload** para desenvolvimento ágil  
✅ **Multi-arquitetura** x64/ARM64 nativa
✅ **Deploy automatizado** com scripts profissionais
✅ **Rollback fácil** em caso de problemas
✅ **Monitoramento** com health checks
✅ **Documentação completa** para toda equipe

---
📚 **Documentação completa**: `docs/DESENVOLVIMENTO_WORKFLOW.md`
