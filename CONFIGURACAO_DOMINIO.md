# Configuração de Domínio - SAGA

## Status Atual
✅ Containers rodando normalmente
✅ Acesso via http://10.166.72.36:8080
✅ Arquivos de configuração criados

## Próximos Passos para Configurar saga.eb.mil.br

### 1. DNS - Configuração no Servidor DNS do Exército

Você precisa criar um registro DNS apontando para o servidor:

```
Tipo: A
Nome: saga.eb.mil.br
Valor: 10.166.72.36
TTL: 3600
```

**Responsável**: Equipe de rede/DNS do 7º CTA

### 2. Baixar imagem do Nginx (quando houver internet)

Execute quando houver conexão:

```bash
# No servidor via tsh
cd /workspace/saga
docker pull nginx:alpine
```

### 3. Ativar o Proxy Reverso

Depois de baixar a imagem do Nginx, execute:

```bash
cd /workspace/saga
docker-compose up -d nginx
```

### 4. Verificar funcionamento

```bash
# Testar o health check
curl http://localhost/health

# Testar acesso local
curl -H Host: saga.eb.mil.br http://localhost

# Ver logs do Nginx
docker-compose logs -f nginx
```

## Configuração Atual do Docker Compose

### Acessos Disponíveis:

| Serviço | Porta | URL |
|---------|-------|-----|
| Aplicação (direto) | 8080 | http://10.166.72.36:8080 |
| Nginx (porta 80) | 80 | http://saga.eb.mil.br (após DNS) |
| Nginx (porta 8080) | 8080 | http://10.166.72.36:8080 (via proxy) |
| PostgreSQL | 5433 | 10.166.72.36:5433 |
| Redis | 6380 | 10.166.72.36:6380 |
| MCP | 3030 | http://10.166.72.36:3030 |

## Para Desenvolvimento Local

Quando desenvolver em outra máquina, você tem 2 opções:

### Opção 1: Usar docker-compose local (SEM proxy)

Descomente as portas do app no docker-compose.yml:

```yaml
app:
    ports:
        - 8080:80  # Descomente esta linha
```

E comente ou remova o serviço nginx:

```yaml
# nginx:  # Comente todo o bloco nginx para dev local
#     image: nginx:alpine
#     ...
```

### Opção 2: Usar arquivo docker-compose separado

Crie `docker-compose.override.yml` para desenvolvimento:

```yaml
services:
  app:
    ports:
      - 8080:80
```

O Docker Compose automaticamente mescla com o arquivo principal.

### Opção 3: Usar variável de ambiente

No .env local:

```bash
COMPOSE_FILE=docker-compose.yml
# Ou para produção:
# COMPOSE_FILE=docker-compose.yml:docker-compose.prod.yml
```

## Arquivos Criados

- `nginx.conf`: Configuração do proxy reverso Nginx
- `docker-compose.yml`: Atualizado com serviço nginx
- `CONFIGURACAO_DOMINIO.md`: Este arquivo (documentação)

## Comandos Úteis

### Gerenciar containers:

```bash
# Status
cd /workspace/saga && docker-compose ps

# Logs
cd /workspace/saga && docker-compose logs -f

# Reiniciar
cd /workspace/saga && docker-compose restart

# Parar/Iniciar
cd /workspace/saga && docker-compose stop
cd /workspace/saga && docker-compose start
```

### Testar DNS:

```bash
# Verificar resolução DNS
nslookup saga.eb.mil.br

# Testar conectividade
ping saga.eb.mil.br

# Testar HTTP
curl -I http://saga.eb.mil.br
```

## Troubleshooting

### Nginx não inicia:

```bash
# Verificar se a imagem existe
docker images | grep nginx

# Se não existir, baixar:
docker pull nginx:alpine

# Verificar logs
docker-compose logs nginx
```

### Domínio não resolve:

1. Verificar se DNS foi configurado
2. Aguardar propagação (pode levar até 24h)
3. Limpar cache DNS local: `sudo systemd-resolve --flush-caches`

### Porta 80 em uso:

```bash
# Verificar o que está usando
sudo ss -tlnp | grep :80

# Parar serviço conflitante
sudo systemctl stop apache2  # ou nginx
```

## Segurança (Futuro)

Para produção, considere:

1. **HTTPS/SSL**: Usar Let's Encrypt
2. **Firewall**: Restringir acesso apenas à rede do EB
3. **Headers de Segurança**: Adicionar no Nginx
4. **Rate Limiting**: Proteger contra ataques
5. **WAF**: Web Application Firewall

---
Última atualização: mer 5 nov 2025, 10:19:53, -03
Servidor: VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO
IP: 10.166.72.36
