'use strict';

const mysql = require('mysql2/promise');

let pool = null;

function getPool() {
  if (!pool) {
    pool = mysql.createPool({
      host:             process.env.DB_HOST     || '127.0.0.1',
      port:     parseInt(process.env.DB_PORT    || '3306', 10),
      user:             process.env.DB_USERNAME || 'root',
      password:         process.env.DB_PASSWORD || '',
      database:         process.env.DB_DATABASE || 'whatscrm',
      waitForConnections: true,
      connectionLimit:  10,
      queueLimit:       0,
      enableKeepAlive:  true,
      keepAliveInitialDelay: 10000,
    });
  }
  return pool;
}

/**
 * Execute a query with parameters against the MySQL pool.
 */
async function query(sql, params = []) {
  const [rows] = await getPool().execute(sql, params);
  return rows;
}

module.exports = {
  getPool,
  query,
};
