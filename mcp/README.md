# SAGA MCP Server

Servidor MCP simples (JSON over HTTP) para expor ferramentas de consulta e criação de reservas do SAGA.

## Endpoints

GET /.well-known/mcp/tools -> lista ferramentas
POST /tool/{name} -> executa ferramenta com JSON de entrada

Ferramentas atuais (somente leitura para consultas):
- bookings.byDate { date }
- bookings.range { start_date, end_date }
- user.lookup { id? email? }
- users.search { q, limit? }
- user.upcomingBookings { user_id, days? }
- stats.dailyCounts { start_date, end_date }
 - users.count { }

## Execução

Instale deps e inicie:

```bash
cd mcp
npm install
npm run dev
```

Configure variáveis de ambiente (usa as do Laravel se executar dentro do mesmo container):
- DB_HOST
- DB_PORT
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD

Você pode copiar `.env.example` para `.env` e ajustar.

Executando dentro do ambiente docker-compose existente (usa container app):

```bash
docker-compose exec app bash -lc 'cd mcp && npm install && npm run start:docker'
```

## Observações
Este servidor expõe duas formas:
1. HTTP simples (não é o protocolo MCP oficial) -> `npm run dev`
2. STDIO JSON-RPC simplificado para clientes MCP -> `npm run mcp:stdio`

Formato JSON-RPC básico:
- tool.list => lista nomes de ferramentas
- tool.call { name, params } => executa ferramenta
- ping => saúde

Exemplo manual:
```bash
echo '{"jsonrpc":"2.0","id":1,"method":"tool.list"}' | npm run mcp:stdio
```

Para operações de escrita futuras: adicionar autenticação antes de expor tools mutáveis.
