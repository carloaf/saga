import http from 'http';
import { TextEncoder } from 'util';

/** Simple JSON-over-HTTP MCP-like server (placeholder protocol) */
export class MCPServer {
  constructor(ctx) {
    this.pool = ctx.pool;
    this.tools = new Map();
  }

  registerTool(name, handler, schema) {
    this.tools.set(name, { handler, schema });
  }

  async start(port) {
    const server = http.createServer(async (req, res) => {
      // CORS headers
      res.setHeader('Access-Control-Allow-Origin', '*');
      res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
      res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
      
      if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
      }
      
      if (req.method === 'GET' && req.url === '/.well-known/mcp/tools') {
        const tools = [...this.tools.entries()].map(([name, v]) => ({ name, schema: v.schema }));
        const accept = req.headers.accept || '';
        
        if (accept.includes('text/html')) {
          // Versão HTML formatada para browser
          const html = `<!DOCTYPE html>
<html><head><title>SAGA MCP Tools</title><style>
body{font-family:Arial,sans-serif;margin:20px;}
h1{color:#333;}
.tool{border:1px solid #ddd;margin:10px 0;padding:15px;border-radius:5px;}
.name{font-weight:bold;color:#007cba;font-size:18px;}
.desc{color:#666;margin:5px 0;}
.input{background:#f5f5f5;padding:10px;border-radius:3px;font-family:monospace;}
</style></head><body>
<h1>🛠️ SAGA MCP Tools</h1>
${tools.map(t => `
<div class="tool">
  <div class="name">${t.name}</div>
  <div class="desc">${t.schema.description}</div>
  <div class="input">Input: ${JSON.stringify(t.schema.input, null, 2)}</div>
</div>`).join('')}
</body></html>`;
          res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
          res.end(html);
        } else {
          // Versão JSON
          res.writeHead(200, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ tools }, null, 2));
        }
        return;
      }
      if (req.method === 'POST' && req.url?.startsWith('/tool/')) {
        const name = decodeURIComponent(req.url.split('/').pop());
        if (!this.tools.has(name)) {
          res.writeHead(404); res.end('Unknown tool'); return;
        }
        const body = await readBody(req);
        try {
          const input = body ? JSON.parse(body) : {};
          const result = await this.tools.get(name).handler(input);
          res.writeHead(200, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ ok: true, result }, null, 2));
        } catch (e) {
          res.writeHead(500, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ ok: false, error: e.message }, null, 2));
        }
        return;
      }
      res.writeHead(404); res.end('Not found');
    });
    await new Promise(r => server.listen(port, r));
  }
}

function readBody(req) {
  return new Promise((resolve, reject) => {
    let data = '';
    req.on('data', chunk => data += chunk);
    req.on('end', () => resolve(data));
    req.on('error', reject);
  });
}
