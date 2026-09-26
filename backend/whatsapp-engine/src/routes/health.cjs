'use strict';

const router = require('express').Router();
const { getActiveSessionIds } = require('../sessions/manager.cjs');

router.get('/health', (_req, res) => {
  const activeSessions = getActiveSessionIds();
  res.json({
    status: 'ok',
    service: 'whatscrm-whatsapp-engine',
    version: '1.0.0',
    uptime: Math.floor(process.uptime()),
    activeSessions: activeSessions.length,
    sessionIds: activeSessions,
    timestamp: new Date().toISOString(),
  });
});

module.exports = router;
