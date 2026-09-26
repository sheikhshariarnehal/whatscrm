'use strict';

/**
 * Middleware that validates the X-Internal-Secret header.
 * Reads BAILEYS_INTERNAL_SECRET from process.env (loaded from Laravel root .env).
 */
function requireInternalSecret(req, res, next) {
  const expectedSecret = process.env.BAILEYS_INTERNAL_SECRET || '';

  if (!expectedSecret) {
    return res.status(500).json({
      success: false,
      message: 'Service misconfigured: BAILEYS_INTERNAL_SECRET not set in .env',
    });
  }

  const provided = req.headers['x-internal-secret'] || '';

  if (!provided || provided !== expectedSecret) {
    return res.status(401).json({
      success: false,
      message: 'Unauthorized: Invalid or missing X-Internal-Secret',
    });
  }

  next();
}

module.exports = { requireInternalSecret };
