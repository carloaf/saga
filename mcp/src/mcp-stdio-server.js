#!/usr/bin/env node
// Experimental MCP stdio server (simplified JSON-RPC 2.0) exposing read-only tools.
import { createPool } from './lib/pool.js';
import { toolBookingsByDate } from './tools/toolBookingsByDate.js';
import { toolBookingsRange } from './tools/toolBookingsRange.js';
import { toolUserLookup } from './tools/toolUserLookup.js';
import { toolUsersSearch } from './tools/toolUsersSearch.js';
import { toolUserUpcoming } from './tools/toolUserUpcoming.js';
import { toolStatsDaily } from './tools/toolStatsDaily.js';

const pool = createPool();

const tools = {
  'bookings.byDate': toolBookingsByDate,
  'bookings.range': toolBookingsRange,
  'user.lookup': toolUserLookup,
  'users.search': toolUsersSearch,
  'user.upcomingBookings': toolUserUpcoming,
  'stats.dailyCounts': toolStatsDaily,
};

// Basic JSON-RPC handler over stdio
process.stdin.setEncoding('utf8');
let buf = '';
process.stdin.on('data', chunk => {
  buf += chunk;
  let idx;
  while ((idx = buf.indexOf('\n')) >= 0) {
    const line = buf.slice(0, idx).trim();
    buf = buf.slice(idx + 1);
    if (!line) continue;
    try { handleMessage(JSON.parse(line)); } catch (e) { send({ jsonrpc:'2.0', error:{ code:-32700, message:'Parse error '+e.message } }); }
  }
});

function send(obj) {
  process.stdout.write(JSON.stringify(obj) + '\n');
}

function handleMessage(msg) {
  if (msg.method === 'tool.list') {
    return send({ jsonrpc:'2.0', id: msg.id, result: Object.keys(tools) });
  }
  if (msg.method === 'tool.call') {
    const { name, params } = msg.params || {};
    if (!tools[name]) return send({ jsonrpc:'2.0', id: msg.id, error:{ code: -32601, message:'Tool not found'} });
    tools[name](pool, params || {})
      .then(result => send({ jsonrpc:'2.0', id: msg.id, result }))
      .catch(err => send({ jsonrpc:'2.0', id: msg.id, error:{ code:-32000, message: err.message } }));
    return;
  }
  if (msg.method === 'ping') return send({ jsonrpc:'2.0', id: msg.id, result:'pong' });
  send({ jsonrpc:'2.0', id: msg.id, error:{ code:-32601, message:'Method not found'} });
}

process.on('SIGINT', () => { pool.end().finally(()=>process.exit(0)); });
