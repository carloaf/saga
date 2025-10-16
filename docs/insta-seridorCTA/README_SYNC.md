# 🔄 Sincronização de Código - Desenvolvimento Remoto

## Como funciona?

- Você edita os arquivos localmente em `/home/augusto/workspace/remote/saga` usando o VSCode.
- O script `./sync-saga.sh` sincroniza as mudanças entre sua máquina e o servidor remoto (`/workspace/saga`).
- Sincronização pode ser manual (quando quiser) ou automática (modo watch).

## Comandos principais

```bash
# Primeira vez: baixa tudo do servidor e abre VSCode
./sync-saga.sh init

# Envia mudanças locais para o servidor
./sync-saga.sh up

# Baixa mudanças do servidor para local
./sync-saga.sh down

# Sincroniza automaticamente a cada 10 segundos
./sync-saga.sh watch
```

## Fluxo recomendado
1. `./sync-saga.sh init` (primeira vez)
2. Editar arquivos no VSCode
3. Em outro terminal: `./sync-saga.sh watch`
4. Suas mudanças serão enviadas automaticamente para o servidor!

---

- O Git funciona normalmente na pasta local.
- Comandos Docker/composer devem ser executados via SSH no servidor.
