# Solução de Domínio Simplificada - SAGA

## Situação Atual
- ✅ Aplicação funcionando em http://10.166.72.36:8080
- ❌ Firewall bloqueia download de imagens Docker
- ❌ Repositórios Alpine também bloqueados

## Solução Implementada: Acesso Direto sem Proxy

### Opção A: Configurar /etc/hosts (Recomendado para testes)

Em cada máquina que precisar acessar, adicionar:

```bash
# Linux/Mac
sudo nano /etc/hosts

# Adicionar a linha:
10.166.72.36  saga.eb.mil.br

# Testar
ping saga.eb.mil.br
curl http://saga.eb.mil.br:8080
```

**Windows:**
```
1. Abrir Bloco de Notas como Administrador
2. Abrir arquivo: C:\Windows\System32\drivers\etc\hosts
3. Adicionar: 10.166.72.36  saga.eb.mil.br
4. Salvar
5. Abrir navegador: http://saga.eb.mil.br:8080
```

### Opção B: Solicitar DNS Oficial

Contatar equipe de rede para criar:

```
Tipo: A
Nome: saga.eb.mil.br
Valor: 10.166.72.36
TTL: 3600
```

Depois todos acessam: **http://saga.eb.mil.br:8080**

### Opção C: Subdomínio com Porta Padrão (Futuro)

Se quiser remover  da URL, será necessário:

1. **DNS:** saga.eb.mil.br -> 10.166.72.36
2. **Alterar porta** no docker-compose.yml:
   ```yaml
   app:
       ports:
           - "80:80"  # em vez de 8080:80
   ```

3. **Reiniciar:**
   ```bash
   cd /workspace/saga
   docker-compose down
   docker-compose up -d
   ```

4. **Acessar:** http://saga.eb.mil.br (sem porta)

## URLs de Acesso

| Método | URL | Status |
|--------|-----|--------|
| IP direto | http://10.166.72.36:8080 | ✅ Funcionando |
| /etc/hosts | http://saga.eb.mil.br:8080 | ✅ Após configurar |
| DNS oficial | http://saga.eb.mil.br:8080 | ⏳ Após solicitar |
| Porta 80 (futuro) | http://saga.eb.mil.br | ⏳ Requer mudança porta |

## Vantagens da Solução Atual

✅ **Sem dependências externas** - não precisa baixar nada  
✅ **Funciona imediatamente** - basta configurar /etc/hosts  
✅ **Simples de manter** - um container a menos  
✅ **Performance melhor** - sem proxy intermediário  
✅ **Desenvolvimento local fácil** - mesma configuração  

## Para Desenvolvimento Local

Desenvolvedores podem usar:

1. **Mesma porta 8080** - não precisa mudar nada
2. **docker-compose.yml** - funciona igual
3. **/etc/hosts local** - para simular o domínio

```yaml
# docker-compose.yml (mesma config produção/dev)
app:
    ports:
        - 8080:80
```

## Comandos Úteis

```bash
# Verificar containers
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO cd /workspace/saga
