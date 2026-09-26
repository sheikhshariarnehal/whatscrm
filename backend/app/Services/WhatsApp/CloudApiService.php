<?php

namespace App\Services\WhatsApp;

use App\Models\MetaCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudApiService
{
    protected string $graphUrl = 'https://graph.facebook.com/v20.0';
    protected MetaCredential $credential;

    public function __construct(MetaCredential $credential)
    {
        $this->credential = $credential;
    }

    /**
     * Factory method to instantiate service for a given workspace credential.
     */
    public static function forWorkspace(int $workspaceId): ?self
    {
        $credential = MetaCredential::where('workspace_id', $workspaceId)
            ->where('status', 'connected')
            ->first();

        return $credential ? new self($credential) : null;
    }

    /**
     * Verify credentials directly against Meta Graph API and fetch phone details.
     */
    public static function verifyAndFetchDetails(string $phoneNumberId, string $accessToken): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->acceptJson()
                ->timeout(15)
                ->get("https://graph.facebook.com/v20.0/{$phoneNumberId}", [
                    'fields' => 'display_phone_number,verified_name,quality_rating,code_verification_status,messaging_limit_tier,is_on_biz_app,platform_type',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            $error = $response->json()['error'] ?? [];
            $msg = $error['message'] ?? 'Meta API validation failed. Please check your Phone Number ID and Access Token.';

            return [
                'success' => false,
                'error' => $msg,
                'code' => $error['code'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'Connection to Meta Graph API failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Auto-subscribe Meta app to WABA webhook notifications.
     */
    public static function subscribeWaba(string $wabaId, string $accessToken): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->acceptJson()
                ->timeout(15)
                ->post("https://graph.facebook.com/v20.0/{$wabaId}/subscribed_apps");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Failed to subscribe webhook to WABA',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch WABA account details (name, currency, timezone, review status).
     */
    public static function fetchWabaDetails(string $wabaId, string $accessToken): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->acceptJson()
                ->timeout(15)
                ->get("https://graph.facebook.com/v20.0/{$wabaId}", [
                    'fields' => 'id,name,currency,timezone_id,account_review_status,message_template_namespace',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Failed to fetch WABA details',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send plain text message to a WhatsApp number.
     */
    public function sendTextMessage(string $to, string $text, ?string $replyToMessageId = null): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($to),
            'type' => 'text',
            'text' => [
                'preview_url' => true,
                'body' => $text,
            ],
        ];

        if ($replyToMessageId) {
            $payload['context'] = [
                'message_id' => $replyToMessageId,
            ];
        }

        return $this->post("/{$this->credential->phone_number_id}/messages", $payload);
    }

    /**
     * Send media message (image, document, audio, video).
     */
    public function sendMediaMessage(string $to, string $type, string $mediaUrl, ?string $caption = null): array
    {
        $mediaPayload = ['link' => $mediaUrl];
        if ($caption && in_array($type, ['image', 'video', 'document'])) {
            $mediaPayload['caption'] = $caption;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($to),
            'type' => $type,
            $type => $mediaPayload,
        ];

        return $this->post("/{$this->credential->phone_number_id}/messages", $payload);
    }

    /**
     * Send pre-approved WhatsApp Cloud template message.
     */
    public function sendTemplateMessage(string $to, string $templateName, string $languageCode = 'en', array $components = []): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($to),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
                'components' => $components,
            ],
        ];

        return $this->post("/{$this->credential->phone_number_id}/messages", $payload);
    }

    /**
     * Mark incoming message as read (blue ticks).
     */
    public function markAsRead(string $messageId): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'status' => 'read',
            'message_id' => $messageId,
        ];

        return $this->post("/{$this->credential->phone_number_id}/messages", $payload);
    }

    /**
     * Fetch templates approved on the WABA.
     */
    public function getMessageTemplates(): array
    {
        return $this->get("/{$this->credential->waba_id}/message_templates", ['limit' => 100]);
    }

    /**
     * Refresh live metadata from Meta Graph API and update credential model.
     */
    public function refreshDetails(): array
    {
        $phoneRes = self::verifyAndFetchDetails($this->credential->phone_number_id, $this->credential->access_token);
        if (!$phoneRes['success']) {
            return $phoneRes;
        }

        $data = $phoneRes['data'];
        $settings = $this->credential->settings ?? [];
        $settings['messaging_limit_tier'] = $data['messaging_limit_tier'] ?? ($settings['messaging_limit_tier'] ?? 'UNKNOWN');
        $settings['code_verification_status'] = $data['code_verification_status'] ?? ($settings['code_verification_status'] ?? 'UNKNOWN');
        $settings['is_on_biz_app'] = $data['is_on_biz_app'] ?? ($settings['is_on_biz_app'] ?? false);
        $settings['platform_type'] = $data['platform_type'] ?? ($settings['platform_type'] ?? 'CLOUD_API');
        $settings['last_synced_at'] = now()->toIso8601String();

        $this->credential->update([
            'display_phone_number' => $data['display_phone_number'] ?? $this->credential->display_phone_number,
            'verified_name'        => $data['verified_name'] ?? $this->credential->verified_name,
            'quality_rating'       => $data['quality_rating'] ?? $this->credential->quality_rating,
            'settings'             => $settings,
            'status'               => 'connected',
        ]);

        return [
            'success' => true,
            'data'    => $this->credential->fresh(),
        ];
    }

    /**
     * Base POST request to Meta Graph API.
     */
    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withToken($this->credential->access_token)
                ->acceptJson()
                ->timeout(15)
                ->post($this->graphUrl . $endpoint, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Meta Cloud API Error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Unknown Meta API error',
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('Meta Cloud API Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Base GET request to Meta Graph API.
     */
    protected function get(string $endpoint, array $query = []): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withToken($this->credential->access_token)
                ->acceptJson()
                ->timeout(15)
                ->get($this->graphUrl . $endpoint, $query);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Unknown Meta API error',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number to international E.164 digits without '+' or symbols.
     */
    protected function cleanPhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
