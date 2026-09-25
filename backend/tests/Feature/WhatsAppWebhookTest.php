<?php

namespace Tests\Feature;

use App\Events\NewMessageReceived;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MetaCredential;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_meta_webhook_verification_handshake(): void
    {
        $challenge = '1122334455';
        $response = $this->get('/api/v1/webhook/whatsapp?' . http_build_query([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'whatscrm_secure_token',
            'hub_challenge' => $challenge,
        ]));

        $response->assertStatus(200);
        $response->assertSee($challenge);
    }

    public function test_inbound_message_creates_contact_conversation_and_message(): void
    {
        Event::fake([NewMessageReceived::class]);

        $workspace = Workspace::where('slug', 'acme-corp')->firstOrFail();

        // Create meta credential for Acme Corp
        MetaCredential::create([
            'workspace_id' => $workspace->id,
            'phone_number_id' => '10987654321',
            'waba_id' => 'waba_999888',
            'access_token' => 'EAAG...',
            'verify_token' => 'whatscrm_secure_token',
            'status' => 'connected',
        ]);

        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'waba_999888',
                    'changes' => [
                        [
                            'field' => 'messages',
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '+15550009999',
                                    'phone_number_id' => '10987654321',
                                ],
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'Elon Musk'],
                                        'wa_id' => '15557778888',
                                    ],
                                ],
                                'messages' => [
                                    [
                                        'from' => '15557778888',
                                        'id' => 'wamid.TEST_MESSAGE_001',
                                        'timestamp' => '1710000000',
                                        'type' => 'text',
                                        'text' => ['body' => 'I would like to order 10 Starlinks.'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/webhook/whatsapp', $payload);
        $response->assertStatus(200);

        // Verify Contact was created under Acme Corp
        $contact = Contact::withoutGlobalScopes()->where('mobile', '+15557778888')->first();
        $this->assertNotNull($contact);
        $this->assertEquals('Elon Musk', $contact->name);
        $this->assertEquals($workspace->id, $contact->workspace_id);

        // Verify Conversation was created
        $conversation = Conversation::withoutGlobalScopes()->where('chat_id', '+15557778888')->first();
        $this->assertNotNull($conversation);
        $this->assertEquals('I would like to order 10 Starlinks.', $conversation->last_message);
        $this->assertEquals(1, $conversation->unread_count);

        // Verify Message was recorded
        $message = Message::withoutGlobalScopes()->where('external_id', 'wamid.TEST_MESSAGE_001')->first();
        $this->assertNotNull($message);
        $this->assertEquals('inbound', $message->direction);
        $this->assertEquals('I would like to order 10 Starlinks.', $message->content);

        // Verify Event was dispatched
        Event::assertDispatched(NewMessageReceived::class);
    }
}
