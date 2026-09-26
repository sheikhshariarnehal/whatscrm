<?php

namespace App\Services\WhatsApp;

use App\Models\MetaCredential;
use Illuminate\Support\Facades\Cache;
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
     * Auto-detect the parent WhatsApp Business Account (WABA) for a given phone number ID.
     */
    public static function findWabaForPhoneNumber(string $phoneNumberId, string $accessToken): ?string
    {
        try {
            $bizRes = Http::withoutVerifying()
                ->withToken($accessToken)
                ->acceptJson()
                ->timeout(10)
                ->get("https://graph.facebook.com/v20.0/me/businesses?fields=id");

            $bizIds = collect($bizRes->json()['data'] ?? [])->pluck('id')->toArray();

            foreach ($bizIds as $bId) {
                $wabaRes = Http::withoutVerifying()
                    ->withToken($accessToken)
                    ->acceptJson()
                    ->timeout(10)
                    ->get("https://graph.facebook.com/v20.0/{$bId}/owned_whatsapp_business_accounts?fields=id,phone_numbers{id}");

                foreach ($wabaRes->json()['data'] ?? [] as $waba) {
                    foreach ($waba['phone_numbers']['data'] ?? [] as $p) {
                        if (($p['id'] ?? '') === $phoneNumberId) {
                            return $waba['id'];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore detection exception and fallback to user-entered WABA
        }

        return null;
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
        // Auto-correct template language and ensure required components are provided
        $templates = $this->getCachedOrSavedTemplates();
        $matched = collect($templates)->firstWhere('name', $templateName);

        if ($matched) {
            // Use exact approved language if caller provided generic 'en'
            if (!empty($matched['language']) && ($languageCode === 'en' || empty($languageCode))) {
                $languageCode = $matched['language'];
            }

            // Ensure required body parameters are never empty (prevents Meta error #132000)
            if (empty($components) && !empty($matched['variables'])) {
                $params = [];
                foreach ($matched['variables'] as $var) {
                    $params[] = ['type' => 'text', 'text' => 'Customer'];
                }
                $components = [
                    [
                        'type' => 'body',
                        'parameters' => $params,
                    ],
                ];
            }
        }

        $templateData = [
            'name' => $templateName,
            'language' => [
                'code' => $languageCode,
            ],
        ];

        if (!empty($components)) {
            $templateData['components'] = $components;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->cleanPhoneNumber($to),
            'type' => 'template',
            'template' => $templateData,
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
     * Fetch WhatsApp profile photo URL for a contact phone number.
     *
     * Uses the v16.0+ contacts API with profile_picture field.
     * Returns null if the user has no photo, has it hidden, or the API call fails.
     */
    public function fetchContactProfilePhotoUrl(string $phoneNumber): ?string
    {
        $clean = $this->cleanPhoneNumber($phoneNumber);

        try {
            // Method 1: Try the newer contacts endpoint that returns profile picture
            $response = Http::withoutVerifying()
                ->withToken($this->credential->access_token)
                ->acceptJson()
                ->timeout(10)
                ->post("{$this->graphUrl}/{$this->credential->phone_number_id}/contacts", [
                    'blocking'  => true,
                    'contacts'  => ["+{$clean}"],
                    'force_check' => false,
                ]);

            if ($response->successful()) {
                $contacts = $response->json('contacts') ?? [];
                foreach ($contacts as $contact) {
                    $profileUrl = $contact['profile']['picture'] ?? null;
                    if ($profileUrl && filter_var($profileUrl, FILTER_VALIDATE_URL)) {
                        return $profileUrl;
                    }
                }
            }

            // Method 2: Try the wa_profile endpoint (available on some API versions)
            $profileRes = Http::withoutVerifying()
                ->withToken($this->credential->access_token)
                ->acceptJson()
                ->timeout(10)
                ->get("{$this->graphUrl}/{$clean}", [
                    'fields' => 'profile_picture_url',
                ]);

            if ($profileRes->successful()) {
                $url = $profileRes->json('profile_picture_url');
                if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                    return $url;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('Profile photo fetch failed for ' . $clean . ': ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch templates approved on the WABA.
     */
    public function getMessageTemplates(): array
    {
        return $this->get("/{$this->credential->waba_id}/message_templates", ['limit' => 100]);
    }

    /**
     * Parse and format Meta templates response into clean structured array.
     */
    public function parseTemplates(array $rawTemplates): array
    {
        $parsed = [];
        foreach ($rawTemplates as $tpl) {
            $body = '';
            $header = null;
            $footer = null;
            $buttons = [];

            foreach ($tpl['components'] ?? [] as $component) {
                $type = strtoupper($component['type'] ?? '');
                if ($type === 'BODY') {
                    $body = $component['text'] ?? '';
                } elseif ($type === 'HEADER') {
                    $header = [
                        'format' => $component['format'] ?? 'TEXT',
                        'text' => $component['text'] ?? null,
                    ];
                } elseif ($type === 'FOOTER') {
                    $footer = $component['text'] ?? null;
                } elseif ($type === 'BUTTONS') {
                    $buttons = $component['buttons'] ?? [];
                }
            }

            // Extract variable placeholders from body: {{1}}, {{2}}, or {{name}}
            preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $body, $matches);
            $variables = array_values(array_unique($matches[1] ?? []));

            $parsed[] = [
                'id' => $tpl['id'] ?? null,
                'name' => $tpl['name'] ?? '',
                'language' => $tpl['language'] ?? 'en_US',
                'status' => $tpl['status'] ?? 'APPROVED',
                'category' => $tpl['category'] ?? 'UTILITY',
                'body' => $body,
                'header' => $header,
                'footer' => $footer,
                'buttons' => $buttons,
                'variables' => $variables,
                'components' => $tpl['components'] ?? [],
            ];
        }

        return $parsed;
    }

    /**
     * Fetch, parse, and persist Meta approved templates to the credential settings and cache.
     */
    public function syncAndSaveTemplates(): array
    {
        $res = $this->getMessageTemplates();
        if (!($res['success'] ?? false)) {
            Log::error('Failed to fetch Meta templates', ['res' => $res]);
            return [
                'success' => false,
                'error' => $res['error'] ?? 'Failed to retrieve templates from Meta Graph API.',
            ];
        }

        $rawTemplates = $res['data']['data'] ?? [];
        $parsed = $this->parseTemplates($rawTemplates);

        $settings = $this->credential->settings ?? [];
        $settings['templates'] = $parsed;
        $settings['templates_count'] = count($parsed);
        $settings['templates_last_synced'] = now()->toIso8601String();

        $this->credential->update(['settings' => $settings]);
        Cache::put("meta_templates_{$this->credential->workspace_id}", $parsed, 86400);

        return [
            'success' => true,
            'count' => count($parsed),
            'templates' => $parsed,
        ];
    }

    /**
     * Retrieve cached or persisted templates, or perform initial sync.
     */
    public function getCachedOrSavedTemplates(): array
    {
        $cached = Cache::get("meta_templates_{$this->credential->workspace_id}");
        if (!empty($cached)) {
            return $cached;
        }

        $saved = $this->credential->settings['templates'] ?? [];
        if (!empty($saved)) {
            Cache::put("meta_templates_{$this->credential->workspace_id}", $saved, 86400);
            return $saved;
        }

        // Try syncing live if connected
        $sync = $this->syncAndSaveTemplates();
        if (($sync['success'] ?? false) && !empty($sync['templates'])) {
            return $sync['templates'];
        }

        return self::getDefaultTemplates();
    }

    /**
     * Default pre-approved starter templates when Meta is not yet connected.
     */
    public static function getDefaultTemplates(): array
    {
        return [
            [
                'id' => 'sample_promo_2026',
                'name' => 'sample_promo_2026',
                'language' => 'en',
                'category' => 'MARKETING',
                'status' => 'APPROVED',
                'body' => 'Hello {{1}}, enjoy exclusive 20% off with promo code VIP2026. Reply STOP to opt out.',
                'variables' => ['1'],
            ],
            [
                'id' => 'order_status_update',
                'name' => 'order_status_update',
                'language' => 'en',
                'category' => 'UTILITY',
                'status' => 'APPROVED',
                'body' => 'Hi {{1}}, your order #{{2}} is currently being packaged and will ship shortly!',
                'variables' => ['1', '2'],
            ],
            [
                'id' => 'service_appointment_reminder',
                'name' => 'service_appointment_reminder',
                'language' => 'en',
                'category' => 'UTILITY',
                'status' => 'APPROVED',
                'body' => 'Dear {{1}}, this is a friendly reminder for your appointment scheduled for {{2}}. Reply 1 to confirm.',
                'variables' => ['1', '2'],
            ],
        ];
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
