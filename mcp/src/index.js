#!/usr/bin/env node
/**
 * Minimal MCP server for SAGA.
 * Implements resources and tools to query bookings and users respecting business rules.
 */
import { MCPServer } from './lib/mcp-server.js';
import { createPool } from './lib/pool.js';
import { registerTools } from './tools/register.js';

const PORT = process.env.MCP_PORT || 3030;

async function main() {
  const pool = createPool();
  const server = new MCPServer({ pool });
  await registerTools(server, pool);
  await server.start(PORT);
  console.log(`[SAGA-MCP] Listening on :${PORT}`);
}

main().catch(err => {
  console.error('[SAGA-MCP] Fatal startup error', err);
  process.exit(1);
});
