'use strict';

const db = require('../database/connection.cjs');
const { getSession } = require('../sessions/manager.cjs');

let timer = null;

function randomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

async function runWarmerTick() {
  try {
    const activeWarmers = await db.query(
      'SELECT id, uid, instances, min_sleep, max_sleep, max_daily FROM warmers WHERE is_active = 1',
    );

    if (!activeWarmers.length) return;

    for (const warmer of activeWarmers) {
      let instanceIds = [];
      try {
        instanceIds = typeof warmer.instances === 'string'
          ? JSON.parse(warmer.instances)
          : (warmer.instances || []);
      } catch (_) {
        continue;
      }

      if (instanceIds.length < 2) continue;

      const [senderId, receiverId] = instanceIds
        .sort(() => Math.random() - 0.5)
        .slice(0, 2);

      const senderSock = getSession(senderId);
      if (!senderSock) continue;

      const receiverRows = await db.query(
        'SELECT number FROM instance WHERE uniqueId = ? AND status = "ACTIVE"',
        [receiverId],
      );
      if (!receiverRows.length || !receiverRows[0].number) continue;

      const receiverPhone = receiverRows[0].number.replace(/[^0-9]/g, '');
      const receiverJid   = `${receiverPhone}@s.whatsapp.net`;

      const scripts = await db.query(
        'SELECT message FROM warmer_scripts WHERE uid = "default" OR uid = ? ORDER BY RAND() LIMIT 1',
        [warmer.uid],
      );

      const text = scripts.length > 0
        ? scripts[0].message
        : 'Hello from WhatsCRM automated number warmer dialogue.';

      try {
        await senderSock.sendMessage(receiverJid, { text });
        console.log(`[Warmer] 💬 ${senderId} -> ${receiverPhone}: "${text.slice(0, 30)}..."`);
      } catch (sendErr) {
        console.error(`[Warmer] Send error (${senderId}):`, sendErr.message);
      }
    }
  } catch (err) {
    console.error('[Warmer] Tick error:', err.message);
  }
}

function startWarmerLoop(intervalMs = 30_000) {
  if (timer) return;
  console.log(`[Baileys] ✅ Warmer loop started (interval: ${intervalMs / 1000}s)`);
  timer = setInterval(runWarmerTick, intervalMs);
}

function stopWarmerLoop() {
  if (timer) {
    clearInterval(timer);
    timer = null;
    console.log('[Baileys] 🛑 Warmer loop stopped');
  }
}

module.exports = {
  startWarmerLoop,
  stopWarmerLoop,
  runWarmerTick,
};
