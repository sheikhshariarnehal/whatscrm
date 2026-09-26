'use strict';

const path = require('path');

// ── 1. Single Source of Truth: Load Laravel's root .env directly ─────────────
const envPath = path.resolve(__dirname, '../.env');
require('dotenv').config({ path: envPath });

const express = require('express');
const { query } = require('./src/database/connection.cjs');
const { initializeSessions } = require('./src/sessions/manager.cjs');
const { startWarmerLoop, stopWarmerLoop } = require('./src/warmer/engine.cjs');

const healthRoutes  = require('./src/routes/health.cjs');
const sessionRoutes = require('./src/routes/session.cjs');

const app  = express();
const PORT = parseInt(process.env.BAILEYS_PORT || '8002', 10);
const HOST = '127.0.0.1';

app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ extended: true, limit: '50mb' }));

// ── 2. Mount Routes ───────────────────────────────────────────────────────────
app.use('/', healthRoutes);
app.use('/session', sessionRoutes);

// ── 3. Start Server ───────────────────────────────────────────────────────────
async function bootstrap() {
  try {
    // Verify MySQL connectivity
    await query('SELECT 1');
    console.log('[WhatsApp Engine] ✅ Database connected (via Laravel .env)');

    // Restore sessions that were ACTIVE before restart
    await initializeSessions();
    console.log('[WhatsApp Engine] ✅ Sessions initialized');

    // Start background number warmer loop
    startWarmerLoop(30_000);

    // Listen on local interface only
    const server = app.listen(PORT, HOST, () => {
      console.log(`[WhatsApp Engine] ✅ Listening on http://${HOST}:${PORT}`);
    });

    // Graceful shutdown
    function shutdown(signal) {
      console.log(`\n[WhatsApp Engine] ${signal} received — shutting down gracefully`);
      stopWarmerLoop();
      server.close(() => {
        console.log('[WhatsApp Engine] HTTP server closed');
        process.exit(0);
      });
    }

    process.on('SIGTERM', () => shutdown('SIGTERM'));
    process.on('SIGINT',  () => shutdown('SIGINT'));

  } catch (err) {
    console.error('[WhatsApp Engine] ❌ Bootstrap failed:', err.message);
    process.exit(1);
  }
}

bootstrap();
