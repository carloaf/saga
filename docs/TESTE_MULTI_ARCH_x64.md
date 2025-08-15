# SAGA - Teste Multi-Arquitetura em Linux x64
# Instruções para clonar e testar em diferentes arquiteturas

## 🎯 OBJETIVO
Testar o projeto SAGA em uma máquina Linux x64 para validar:
- ✅ Compatibilidade multi-arquitetura 
- ✅ Build automático em diferentes plataformas
- ✅ Funcionamento dos ambientes (dev/staging)
- ✅ Scripts de deployment

## 🖥️ INSTRUÇÕES PARA MÁQUINA LINUX X64

### 1. **Pré-requisitos**
```bash
# Verificar arquitetura
uname -m
# Deve mostrar: x86_64

# Instalar Docker (se não estiver instalado)
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
# Logout e login novamente

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### 2. **Clone do Repositório**
```bash
# Clone do projeto
git clone https://github.com/carloaf/saga.git
cd saga

# Verificar branch
git branch -a
git checkout dev  # Se não estiver em dev

# Verificar commits recentes
git log --oneline -3
# Deve mostrar nossos 3 commits:
# - docs: create comprehensive multi-architecture development documentation
# - feat(deploy): implement professional multi-architecture deployment structure  
# - chore: cleanup project structure and remove temporary files
```

### 3. **Build e Teste do Ambiente de Desenvolvimento**
```bash
# Build das imagens (vai detectar x64 automaticamente)
docker compose build

# Verificar se a imagem foi criada
docker images | grep saga

# Iniciar ambiente de desenvolvimento
docker compose up -d

# Aguardar containers ficarem healthy
sleep 30

# Verificar status
docker ps | grep saga
# Deve mostrar saga_app_dev como healthy

# Testar aplicação
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8000
# Deve retornar: HTTP 200
```

### 4. **Teste do Ambiente de Staging**
```bash
# Iniciar staging
cd deploy/staging
docker compose -f docker-compose.staging.yml up -d

# Aguardar containers
sleep 30

# Verificar status
docker ps | grep staging

# Testar staging
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8080
# Deve retornar: HTTP 200

# Voltar para raiz
cd ../..
```

### 5. **Teste dos Scripts Multi-Arquitetura**
```bash
# Dar permissão aos scripts
chmod +x scripts/deployment/*.sh

# Testar script de build multi-arch
./scripts/deployment/build-multiarch.sh --platforms linux/amd64

# Verificar se buildx está funcionando
docker buildx ls
# Deve mostrar builder ativo
```

### 6. **Validação Completa**
```bash
# Status de todos os containers
echo "=== CONTAINER STATUS ==="
docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Status}}\t{{.Ports}}"

# Teste dos endpoints
echo "=== HTTP TESTS ==="
echo "Development:" && curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8000
echo "Staging:" && curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8080

# Verificar logs
echo "=== LOGS CHECK ==="
docker logs saga_app_dev --tail 5
docker logs saga_app_staging --tail 5
```

## 🔍 **Resultados Esperados**

### ✅ **Sucesso Multi-Arquitetura:**
```bash
# Architecture
uname -m: x86_64

# Containers
saga_app_dev: Up X minutes (healthy)
saga_app_staging: Up X minutes (healthy) 

# HTTP Tests
Development: HTTP 200
Staging: HTTP 200

# Images
saga/app:dev - linux/amd64
saga/app:staging - linux/amd64
```

### 🏗️ **Diferenças vs ARM64 (Raspberry Pi):**
- **Arquitetura**: x86_64 vs aarch64
- **Performance**: Potencialmente mais rápido build/startup
- **Compatibilidade**: Mesma funcionalidade, detectada automaticamente
- **Docker**: Usa imagens nativas x64 (sem emulação)

## 🐛 **Troubleshooting x64**

### Problema: Build falha
```bash
# Limpar cache Docker
docker system prune -a

# Rebuild sem cache
docker compose build --no-cache
```

### Problema: Containers unhealthy  
```bash
# Verificar logs detalhados
docker logs saga_app_dev

# Verificar configurações de rede
docker network ls
docker network inspect saga_saga_network
```

### Problema: Portas ocupadas
```bash
# Verificar portas em uso
sudo netstat -tulpn | grep :8000
sudo netstat -tulpn | grep :8080

# Parar containers conflitantes
docker stop $(docker ps -q)
```

## 📊 **Benchmark Multi-Arquitetura**

### Métricas para Comparar:
```bash
# Tempo de build
time docker compose build

# Tempo de startup
time docker compose up -d

# Performance HTTP
time curl http://localhost:8000

# Uso de recursos
docker stats --no-stream
```

## ✅ **Checklist de Validação**

### Funcionalidades Básicas:
- [ ] Clone do repositório funcionou
- [ ] Build das imagens funcionou
- [ ] Containers sobem healthy
- [ ] Development responde HTTP 200
- [ ] Staging responde HTTP 200
- [ ] Scripts multi-arch funcionam
- [ ] Logs não mostram erros críticos

### Multi-Arquitetura:
- [ ] Build automático detecta x64
- [ ] Imagens são nativas (não emuladas)
- [ ] Performance adequada
- [ ] Sem warnings de arquitetura

### Workflow:
- [ ] Documentação está acessível
- [ ] Comandos funcionam conforme docs
- [ ] Estrutura de projeto está organizada
- [ ] Git history mostra commits organizados

## 🎯 **Próximos Passos Após Validação**

1. **✅ Se tudo funcionar**: 
   - Projeto validado em multi-arquitetura
   - Workflow pronto para desenvolvimento
   - Deploy scripts testados

2. **❌ Se houver problemas**:
   - Documentar issues específicas do x64
   - Ajustar configurações se necessário
   - Atualizar documentação

3. **🚀 Preparar para Produção**:
   - Testar build para produção
   - Configurar CI/CD se necessário
   - Documentar processo de deploy

---
📝 **Criado em**: 15 de Agosto de 2025  
🏗️ **Testado em**: ARM64 (Raspberry Pi) ✅  
🎯 **Para testar**: x64 Linux ⏳
