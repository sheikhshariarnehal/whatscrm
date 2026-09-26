'use strict';

const pino   = require('pino');
const qrcode = require('qrcode');
const axios  = require('axios');
const db     = require('../database/connection.cjs');

// ── Lazy-loaded Baileys ────────────────────────────────────────────────────────
let baileys = null;
async function loadBaileys() {
  if (!baileys) {
    baileys = await import('@whiskeysockets/baileys');
  }
  return baileys;
}

// ── Lazy-loaded mysql-baileys auth state ───────────────────────────────────────
let useMySQLAuthState = null;
function getMySQLAuthState() {
  if (!useMySQLAuthState) {
    useMySQLAuthState = require('mysql-baileys').useMySQLAuthState;
  }
  return useMySQLAuthState;
}

// ── In-memory active session sockets ──────────────────────────────────────────
const activeSessions = new Map();

// ── Helpers ───────────────────────────────────────────────────────────────────

function extractPhone(waId) {
  if (!waId) return null;
  const match = String(waId).match(/^(\d+)(?::|@)/);
  return match ? match[1] : null;
}

function getDbConfig() {
  return {
    host:                process.env.DB_HOST     || '127.0.0.1',
    port:        parseInt(process.env.DB_PORT    || '3306', 10),
    user:                process.env.DB_USERNAME || 'root',
    password:            process.env.DB_PASSWORD || '',
    database:            process.env.DB_DATABASE || 'whatscrm',
    tableName:           'auth',
    retryRequestDelayMs: 200,
  };
}

// ── Create or Restore a Session ───────────────────────────────────────────────

async function createSession(uniqueId, title = 'WhatsCRM', uid = '') {
  if (activeSessions.has(uniqueId)) {
    console.log(`[Session] ⚡ Already active: ${uniqueId}`);
    return;
  }

  console.log(`[Session] 🚀 Creating session: ${uniqueId}`);

  try {
    const {
      default: makeWASocket,
      DisconnectReason,
      fetchLatestBaileysVersion,
      makeCacheableSignalKeyStore,
    } = await loadBaileys();

    const { version } = await fetchLatestBaileysVersion();
    const authStateFactory = getMySQLAuthState();

    const { state, saveCreds, removeCreds } = await authStateFactory({
      ...getDbConfig(),
      session: uniqueId,
    });

    const logger = pino({ level: 'silent' });

    const sock = makeWASocket({
      version,
      logger,
      auth: {
        creds: state.creds,
        keys:  makeCacheableSignalKeyStore(state.keys, logger),
      },
      browser:                    [title.slice(0, 20), '', ''],
      printQRInTerminal:          false,
      syncFullHistory:            false,
      defaultQueryTimeoutMs:      60_000,
      connectTimeoutMs:           60_000,
      keepAliveIntervalMs:        30_000,
      markOnlineOnConnect:        true,
      generateHighQualityLinkPreview: true,
    });

    activeSessions.set(uniqueId, sock);

    sock.ev.on('creds.update', saveCreds);

    // ── Inbound Messages ──────────────────────────────────────────────────────
    sock.ev.on('messages.upsert', async ({ messages, type }) => {
      if (type !== 'notify') return;

      for (const m of messages) {
        if (!m.message || m.key.fromMe) continue;

        const remoteJid = m.key.remoteJid;
        if (!remoteJid || remoteJid === 'status@broadcast' || remoteJid.includes('broadcast')) {
          continue;
        }

        const phone = extractPhone(remoteJid);
        if (!phone) continue;

        const senderName = m.pushName || phone;
        const text = m.message.conversation ||
          m.message.extendedTextMessage?.text ||
          m.message.imageMessage?.caption ||
          m.message.videoMessage?.caption ||
          m.message.documentMessage?.caption ||
          '';

        const messageId = m.key.id;
        const laravelUrl = process.env.APP_URL || 'http://127.0.0.1:8000';

        try {
          await axios.post(`${laravelUrl}/api/v1/webhook/baileys`, {
            uniqueId,
            uid,
            from: phone,
            name: senderName,
            text,
            messageId,
            timestamp: m.messageTimestamp || Math.floor(Date.now() / 1000),
          }, {
            headers: {
              'X-Internal-Secret': process.env.BAILEYS_INTERNAL_SECRET || '',
              'Content-Type': 'application/json',
            },
            timeout: 5000,
          });
          console.log(`[Session] 📩 Message from ${phone} routed to Laravel`);
        } catch (err) {
          console.log(`[Session] Inbound message webhook notice: ${err.message}`);
        }
      }
    });

    // ── Connection lifecycle ──────────────────────────────────────────────────
    sock.ev.on('connection.update', async ({ connection, lastDisconnect, qr }) => {
      if (qr) {
        try {
          const qrDataUrl = await qrcode.toDataURL(qr);
          await db.query('UPDATE instance SET qr = ? WHERE uniqueId = ?', [qrDataUrl, uniqueId]);
          console.log(`[Session] 📷 QR updated for: ${uniqueId}`);
        } catch (err) {
          console.error(`[Session] QR write error:`, err.message);
        }
      }

      if (connection === 'open') {
        const phone = extractPhone(sock.user?.id);
        const userData = sock.user || {};
        try {
          await db.query(
            'UPDATE instance SET status = ?, number = ?, data = ?, qr = NULL WHERE uniqueId = ?',
            ['ACTIVE', phone, JSON.stringify(userData), uniqueId],
          );
          console.log(`[Session] ✅ Connected: ${uniqueId} | Phone: ${phone}`);
        } catch (err) {
          console.error(`[Session] DB write error (open):`, err.message);
        }
      }

      if (connection === 'close') {
        const statusCode = lastDisconnect?.error?.output?.statusCode;
        const { loggedOut, connectionReplaced } = DisconnectReason;

        activeSessions.delete(uniqueId);

        if (statusCode === loggedOut || statusCode === connectionReplaced) {
          console.log(`[Session] 🔴 Logged out: ${uniqueId}`);
          try { await removeCreds(); } catch (_) {}
          try {
            await db.query('UPDATE instance SET status = ?, qr = NULL WHERE uniqueId = ?', ['INACTIVE', uniqueId]);
          } catch (err) {
            console.error(`[Session] DB write error (logout):`, err.message);
          }
        } else {
          console.log(`[Session] 🔄 Reconnecting in 5s: ${uniqueId} (code: ${statusCode})`);
          setTimeout(() => createSession(uniqueId, title, uid), 5_000);
        }
      }
    });

  } catch (err) {
    activeSessions.delete(uniqueId);
    console.error(`[Session] ❌ createSession failed for ${uniqueId}:`, err.message);
    try {
      await db.query('UPDATE instance SET status = ? WHERE uniqueId = ?', ['INACTIVE', uniqueId]);
    } catch (_) {}
  }
}

// ── Delete / Disconnect a Session ────────────────────────────────────────────

async function deleteSession(uniqueId) {
  console.log(`[Session] 🗑  Deleting session: ${uniqueId}`);
  const sock = activeSessions.get(uniqueId);

  if (sock) {
    try { await sock.logout(); } catch (_) {}
    activeSessions.delete(uniqueId);
  }

  try {
    const authStateFactory = getMySQLAuthState();
    const { removeCreds } = await authStateFactory({ ...getDbConfig(), session: uniqueId });
    await removeCreds();
  } catch (_) {}

  try {
    await db.query('UPDATE instance SET status = ?, qr = NULL WHERE uniqueId = ?', ['INACTIVE', uniqueId]);
  } catch (err) {
    console.error(`[Session] DB write error (delete):`, err.message);
  }
}

// ── Restore Active Sessions on Startup ────────────────────────────────────────

async function initializeSessions() {
  try {
    const rows = await db.query(
      "SELECT uniqueId, title, uid FROM instance WHERE status = 'ACTIVE'",
    );
    console.log(`[Session] 🔍 Found ${rows.length} active session(s) to restore`);
    for (const row of rows) {
      await createSession(row.uniqueId, row.title || 'WhatsCRM', row.uid || '');
    }
  } catch (err) {
    console.error('[Session] initializeSessions error:', err.message);
  }
}

// ── Send Message ─────────────────────────────────────────────────────────────

async function sendMessage(uniqueId, to, content = {}) {
  const sock = activeSessions.get(uniqueId);
  if (!sock) {
    throw new Error(`WhatsApp session "${uniqueId}" is not active or connected`);
  }

  let jid = String(to).trim();
  if (!jid.includes('@')) {
    jid = `${jid.replace(/[^0-9]/g, '')}@s.whatsapp.net`;
  }

  let msgPayload = {};
  if (content.text) {
    msgPayload = { text: content.text };
  } else if (content.mediaUrl && (content.type === 'image' || !content.type)) {
    msgPayload = {
      image: { url: content.mediaUrl },
      caption: content.caption || '',
    };
  } else if (content.mediaUrl && content.type === 'document') {
    msgPayload = {
      document: { url: content.mediaUrl },
      caption: content.caption || '',
      mimetype: content.mimetype || 'application/octet-stream',
      fileName: content.fileName || 'document',
    };
  } else {
    throw new Error('Unsupported message payload. Must provide text or mediaUrl.');
  }

  const sent = await sock.sendMessage(jid, msgPayload);
  return {
    success: true,
    messageId: sent?.key?.id || null,
    timestamp: sent?.messageTimestamp || Math.floor(Date.now() / 1000),
  };
}

// ── Public Accessors ──────────────────────────────────────────────────────────

function getSession(uniqueId) {
  return activeSessions.get(uniqueId) || null;
}

function getActiveSessionIds() {
  return [...activeSessions.keys()];
}

module.exports = {
  createSession,
  deleteSession,
  initializeSessions,
  getSession,
  getActiveSessionIds,
  sendMessage,
};
