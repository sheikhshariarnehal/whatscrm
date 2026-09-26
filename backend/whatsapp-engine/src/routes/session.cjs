'use strict';

const router = require('express').Router();
const { requireInternalSecret } = require('../middleware/auth.cjs');
const { createSession, deleteSession, getActiveSessionIds, sendMessage } = require('../sessions/manager.cjs');
const db = require('../database/connection.cjs');

router.use(requireInternalSecret);

// ── POST /session/create ──────────────────────────────────────────────────────
router.post('/create', async (req, res) => {
  try {
    const { uniqueId, title = 'WhatsCRM', uid = '' } = req.body;

    if (!uniqueId) {
      return res.status(400).json({ success: false, message: 'uniqueId is required' });
    }

    createSession(uniqueId, title, uid).catch(err => {
      console.error(`[Session Route] createSession error: ${err.message}`);
    });

    res.json({ success: true, message: 'Session creation initiated', uniqueId });
  } catch (err) {
    console.error('[POST /session/create]', err.message);
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /session/delete ──────────────────────────────────────────────────────
router.post('/delete', async (req, res) => {
  try {
    const { uniqueId } = req.body;
    if (!uniqueId) return res.status(400).json({ success: false, message: 'uniqueId is required' });

    await deleteSession(uniqueId);
    res.json({ success: true, message: 'Session deleted', uniqueId });
  } catch (err) {
    console.error('[POST /session/delete]', err.message);
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /session/status ───────────────────────────────────────────────────────
router.get('/status', async (req, res) => {
  try {
    const { uniqueId } = req.query;
    if (!uniqueId) return res.status(400).json({ success: false, message: 'uniqueId required' });

    const rows = await db.query(
      'SELECT uniqueId, title, number, status, qr FROM instance WHERE uniqueId = ?',
      [uniqueId],
    );

    if (!rows.length) return res.status(404).json({ success: false, message: 'Instance not found' });

    const row = rows[0];
    res.json({
      success: true,
      data: {
        uniqueId:     row.uniqueId,
        title:        row.title,
        number:       row.number,
        status:       row.status,
        hasQr:        !!row.qr,
        qr:           row.qr || null,
        socketActive: getActiveSessionIds().includes(uniqueId),
      },
    });
  } catch (err) {
    console.error('[GET /session/status]', err.message);
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /session/all ──────────────────────────────────────────────────────────
router.get('/all', (_req, res) => {
  res.json({ success: true, activeSessions: getActiveSessionIds() });
});

// ── POST /session/send-message ────────────────────────────────────────────────
router.post('/send-message', async (req, res) => {
  try {
    const { uniqueId, to, text, mediaUrl, caption, type, mimetype, fileName } = req.body;
    if (!uniqueId || !to) {
      return res.status(400).json({ success: false, message: 'uniqueId and to are required' });
    }

    const result = await sendMessage(uniqueId, to, { text, mediaUrl, caption, type, mimetype, fileName });
    res.json({ success: true, ...result });
  } catch (err) {
    console.error('[POST /session/send-message]', err.message);
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
