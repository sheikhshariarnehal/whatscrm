<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Laravel facade for the WhatsCRM Baileys microservice.
 *
 * All methods are best-effort: if the baileys-service is offline,
 * they return a graceful failure response rather than throwing.
 *
 * The service communicates over localhost HTTP using a shared internal secret.
 * No JWT, no user auth — this is service-to-service only.
 */
class BaileysService
{
    private string $baseUrl;
    private string $secret;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.baileys.url', 'http://127.0.0.1:8002'), '/');
        $this->secret  = config('services.baileys.secret', '');
        $this->timeout = (int) config('services.baileys.timeout', 5);
    }

    // ── Health ────────────────────────────────────────────────────────────────

    /**
     * Check whether the Baileys service is reachable.
     * Returns ['online' => bool, 'activeSessions' => int, 'uptime' => int]
     */
    public function health(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/health");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'online'         => true,
                    'activeSessions' => $data['activeSessions'] ?? 0,
                    'uptime'         => $data['uptime'] ?? 0,
                    'sessionIds'     => $data['sessionIds'] ?? [],
                ];
            }
        } catch (\Throwable $e) {
            Log::debug("BaileysService: health check failed — {$e->getMessage()}");
        }

        return ['online' => false, 'activeSessions' => 0, 'uptime' => 0, 'sessionIds' => []];
    }

    // ── Session lifecycle ─────────────────────────────────────────────────────

    /**
     * Tell the Baileys service to create/restore a WhatsApp session.
     * The instance row in the DB must already exist before calling this.
     *
     * @param  string  $uniqueId  Instance unique identifier
     * @param  string  $title     Friendly device name (max 20 chars)
     * @param  string  $uid       Workspace UID
     */
    public function createSession(string $uniqueId, string $title, string $uid): bool
    {
        try {
            $response = $this->http()
                ->post("{$this->baseUrl}/session/create", [
                    'uniqueId' => $uniqueId,
                    'title'    => mb_substr($title, 0, 20),
                    'uid'      => $uid,
                ]);

            return $response->successful() && ($response->json('success') === true);
        } catch (\Throwable $e) {
            Log::warning("BaileysService: createSession failed — {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Tell the Baileys service to logout and delete a session.
     * Sets instance.status = INACTIVE and removes auth credentials.
     *
     * @param  string  $uniqueId  Instance unique identifier
     */
    public function deleteSession(string $uniqueId): bool
    {
        try {
            $response = $this->http()
                ->post("{$this->baseUrl}/session/delete", [
                    'uniqueId' => $uniqueId,
                ]);

            return $response->successful() && ($response->json('success') === true);
        } catch (\Throwable $e) {
            Log::warning("BaileysService: deleteSession failed — {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Fetch the current live status of one session from the Baileys service.
     * Returns the full data array or null if unavailable.
     */
    public function sessionStatus(string $uniqueId): ?array
    {
        try {
            $response = $this->http()
                ->get("{$this->baseUrl}/session/status", ['uniqueId' => $uniqueId]);

            if ($response->successful()) {
                return $response->json('data');
            }
        } catch (\Throwable $e) {
            Log::debug("BaileysService: sessionStatus failed — {$e->getMessage()}");
        }

        return null;
    }

    // ── Messaging ────────────────────────────────────────────────────────────

    /**
     * Send an outbound text message via an active Baileys WhatsApp session.
     *
     * @param string $uniqueId Session uniqueId
     * @param string $to Recipient phone number
     * @param string $text Message content
     * @return array ['success' => bool, 'messageId' => ?string, 'error' => ?string]
     */
    public function sendTextMessage(string $uniqueId, string $to, string $text): array
    {
        try {
            $response = $this->http()->post("{$this->baseUrl}/session/send-message", [
                'uniqueId' => $uniqueId,
                'to'       => $to,
                'text'     => $text,
            ]);

            if ($response->successful() && $response->json('success')) {
                return [
                    'success'   => true,
                    'messageId' => $response->json('messageId'),
                ];
            }

            return [
                'success' => false,
                'error'   => $response->json('message') ?? 'Failed to send WhatsApp message via Baileys',
            ];
        } catch (\Throwable $e) {
            Log::warning("BaileysService: sendTextMessage failed — {$e->getMessage()}");
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Send an outbound media message via an active Baileys WhatsApp session.
     *
     * @param string $uniqueId Session uniqueId
     * @param string $to Recipient phone number
     * @param string $mediaUrl URL of the media file
     * @param string $caption Optional caption
     * @param string $type Media type ('image' or 'document')
     * @return array ['success' => bool, 'messageId' => ?string, 'error' => ?string]
     */
    public function sendMediaMessage(string $uniqueId, string $to, string $mediaUrl, string $caption = '', string $type = 'image'): array
    {
        try {
            $response = $this->http()->post("{$this->baseUrl}/session/send-message", [
                'uniqueId' => $uniqueId,
                'to'       => $to,
                'mediaUrl' => $mediaUrl,
                'caption'  => $caption,
                'type'     => $type,
            ]);

            if ($response->successful() && $response->json('success')) {
                return [
                    'success'   => true,
                    'messageId' => $response->json('messageId'),
                ];
            }

            return [
                'success' => false,
                'error'   => $response->json('message') ?? 'Failed to send media via Baileys',
            ];
        } catch (\Throwable $e) {
            Log::warning("BaileysService: sendMediaMessage failed — {$e->getMessage()}");
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function http(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout($this->timeout)
            ->withHeaders(['X-Internal-Secret' => $this->secret]);
    }
}
