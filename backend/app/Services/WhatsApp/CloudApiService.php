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
        return $this->get("/{$this->credential->waba_id}/message_templates");
    }

    /**
     * Base POST request to Meta Graph API.
     */
    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::withToken($this->credential->access_token)
                ->acceptJson()
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
            $response = Http::withToken($this->credential->access_token)
                ->acceptJson()
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
