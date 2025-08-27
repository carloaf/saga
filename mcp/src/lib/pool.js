import pg from 'pg';
const { Pool } = pg;

export function createPool() {
  const {
    DB_HOST = 'localhost',
    DB_PORT = '5432',
    DB_DATABASE = 'saga_dev',
    DB_USERNAME = 'postgres',
    DB_PASSWORD = 'postgres'
  } = process.env;

  return new Pool({
    host: DB_HOST,
    port: Number(DB_PORT),
    database: DB_DATABASE,
    user: DB_USERNAME,
    password: DB_PASSWORD,
    max: 10,
    idleTimeoutMillis: 30_000,
    connectionTimeoutMillis: 5_000
  });
}
