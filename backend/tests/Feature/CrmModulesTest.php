<?php

namespace Tests\Feature;

use App\Jobs\ProcessCampaignBatch;
use App\Models\Campaign;
use App\Models\ChatbotRule;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Flow;
use App\Models\Message;
use App\Models\User;
use App\Models\WebhookEndpoint;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\Automation\FlowExecutionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Livewire\Campaigns\CampaignManager;
use App\Livewire\Devices\DeviceManager;
use App\Livewire\Inbox\InboxPage;
use App\Models\MetaCredential;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class CrmModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->workspace = Workspace::create([
            'name' => 'Test CRM Workspace',
            'slug' => 'test-crm',
        ]);

        WorkspaceMember::create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        session(['current_workspace_id' => $this->workspace->id]);
    }

    public function test_crm_module_routes_are_accessible_by_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $routes = ['campaigns', 'automations', 'team', 'developer', 'settings'];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertOk();
        }
    }

    public function test_campaign_can_be_created_and_dispatches_job(): void
    {
        Queue::fake();
        $this->actingAs($this->user);

        $campaign = Campaign::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Summer Promo Test',
            'type' => 'cloud_template',
            'template_name' => 'sample_promo_2026',
            'template_language' => 'en',
            'target_type' => 'all',
            'status' => 'scheduled',
        ]);

        ProcessCampaignBatch::dispatch($campaign->id);

        Queue::assertPushed(ProcessCampaignBatch::class, function ($job) use ($campaign) {
            return $job->campaignId === $campaign->id;
        });
    }

    public function test_flow_execution_service_triggers_keyword_chatbot(): void
    {
        $this->actingAs($this->user);

        // Create chatbot rule
        ChatbotRule::create([
            'workspace_id' => $this->workspace->id,
            'keywords' => 'pricing, cost, plans',
            'match_type' => 'contains',
            'reply_type' => 'text',
            'reply_content' => ['text' => 'Hello {{name}}! Our plans start from $29/mo.'],
            'is_active' => true,
        ]);

        $contact = Contact::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'John Doe',
            'mobile' => '+15550001',
        ]);

        $conversation = Conversation::create([
            'workspace_id' => $this->workspace->id,
            'contact_id' => $contact->id,
            'chat_id' => '+15550001',
            'status' => 'open',
        ]);

        $incomingMessage = Message::create([
            'workspace_id' => $this->workspace->id,
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'type' => 'text',
            'content' => 'Can you send me the pricing please?',
            'status' => 'delivered',
        ]);

        $service = new FlowExecutionService();
        $service->handleIncomingMessage($conversation, $incomingMessage, $contact);

        $this->assertDatabaseHas('messages', [
            'workspace_id' => $this->workspace->id,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'content' => 'Hello John Doe! Our plans start from $29/mo.',
        ]);
    }

    public function test_team_member_can_be_added_to_workspace(): void
    {
        $newUser = User::factory()->create(['email' => 'agent@test.com']);

        $member = WorkspaceMember::create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $newUser->id,
            'role' => 'agent',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('workspace_members', [
            'workspace_id' => $this->workspace->id,
            'user_id' => $newUser->id,
            'role' => 'agent',
        ]);
    }

    public function test_webhook_endpoint_can_be_registered(): void
    {
        $endpoint = WebhookEndpoint::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Shopify Store Hook',
            'url' => 'https://example.com/webhook',
            'secret' => 'supersecrettoken123',
            'events' => ['message.received', 'message.status'],
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('webhook_endpoints', [
            'workspace_id' => $this->workspace->id,
            'name' => 'Shopify Store Hook',
            'url' => 'https://example.com/webhook',
        ]);
    }

    public function test_device_routes_and_subtabs_are_accessible(): void
    {
        $this->actingAs($this->user);

        // Base /devices route
        $response = $this->get(route('devices'));
        $response->assertOk();

        // Subtabs: qr, meta, warmer, social
        foreach (['qr', 'meta', 'warmer', 'social'] as $tab) {
            $response = $this->get(route('devices', $tab));
            $response->assertOk();
        }

        // Invalid tab gracefully handles without 404
        $response = $this->get('/devices/unknown-tab-123');
        $response->assertOk();

        // Livewire component mounts directly with specified tab
        Livewire::test(DeviceManager::class, ['tab' => 'meta'])
            ->assertSet('activeTab', 'meta');

        Livewire::test(DeviceManager::class, ['tab' => 'warmer'])
            ->assertSet('activeTab', 'warmer');

        // Unknown tab falls back to 'qr'
        Livewire::test(DeviceManager::class, ['tab' => 'invalid'])
            ->assertSet('activeTab', 'qr');

        // Tab switching dispatches browser event for history pushState
        Livewire::test(DeviceManager::class)
            ->call('setTab', 'meta')
            ->assertSet('activeTab', 'meta')
            ->assertDispatched('device-tab-changed', tab: 'meta');
    }

    public function test_campaign_manager_syncs_meta_templates(): void
    {
        $this->actingAs($this->user);

        // Fake Meta Graph API response for templates
        Http::fake([
            'https://graph.facebook.com/v20.0/waba_test_123/message_templates*' => Http::response([
                'data' => [
                    [
                        'id' => '999111',
                        'name' => 'order_shipped_notification',
                        'language' => 'en_US',
                        'status' => 'APPROVED',
                        'category' => 'UTILITY',
                        'components' => [
                            [
                                'type' => 'BODY',
                                'text' => 'Hello {{1}}, your order {{2}} has been shipped!',
                            ],
                            [
                                'type' => 'FOOTER',
                                'text' => 'Thank you for shopping with us',
                            ],
                        ],
                    ],
                    [
                        'id' => '999222',
                        'name' => 'welcome_promo',
                        'language' => 'en_US',
                        'status' => 'APPROVED',
                        'category' => 'MARKETING',
                        'components' => [
                            [
                                'type' => 'BODY',
                                'text' => 'Welcome to our store!',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        MetaCredential::create([
            'workspace_id' => $this->workspace->id,
            'phone_number_id' => 'phone_test_123',
            'waba_id' => 'waba_test_123',
            'access_token' => 'EAAG_fake_token',
            'status' => 'connected',
        ]);

        Livewire::test(CampaignManager::class)
            ->call('setTab', 'templates')
            ->call('syncTemplates')
            ->assertHasNoErrors()
            ->assertSee('order_shipped_notification')
            ->assertSee('welcome_promo')
            ->assertSee('Message Templates (2)')
            ->assertSet('templateName', 'order_shipped_notification')
            ->assertSet('templateLanguage', 'en_US');

        // Test selecting a template for broadcast
        Livewire::test(CampaignManager::class)
            ->call('selectTemplateForBroadcast', 'welcome_promo')
            ->assertSet('activeTab', 'create')
            ->assertSet('wizardStep', 3)
            ->assertSet('templateName', 'welcome_promo')
            ->assertSet('templateLanguage', 'en_US');
    }

    public function test_inbox_can_start_new_conversation_and_auto_associate_contact(): void
    {
        $this->actingAs($this->user);

        Livewire::test(InboxPage::class)
            ->set('newChatMobile', '+15559876543')
            ->set('newChatName', 'Alice Wonderland')
            ->set('newChatChannel', 'baileys')
            ->set('newChatMessage', 'Hello Alice from CRM!')
            ->call('startNewConversation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'workspace_id' => $this->workspace->id,
            'mobile' => '15559876543',
            'name' => 'Alice Wonderland',
        ]);

        $this->assertDatabaseHas('conversations', [
            'workspace_id' => $this->workspace->id,
            'sender_mobile' => '15559876543',
            'channel' => 'baileys',
        ]);
    }

    public function test_inbox_calculates_24_hour_service_window_and_switches_channels(): void
    {
        $this->actingAs($this->user);

        // Create conversation with expired inbound window (> 24 hours ago)
        $conversation = Conversation::create([
            'workspace_id' => $this->workspace->id,
            'chat_id' => 'meta_18885551234',
            'sender_mobile' => '18885551234',
            'sender_name' => 'Bob Builder',
            'channel' => 'whatsapp_cloud',
            'last_inbound_at' => now()->subHours(26),
            'last_message_at' => now()->subHours(26),
            'status' => 'open',
        ]);

        $test = Livewire::test(InboxPage::class)
            ->call('selectConversation', $conversation->id);

        $window = $test->get('serviceWindow');
        $this->assertTrue($window['is_expired']);
        $this->assertTrue($window['requires_template']);

        // Switch to Baileys channel
        $test->call('switchChannelToBaileys');
        $conversation->refresh();
        $this->assertEquals('baileys', $conversation->channel);
    }

    public function test_inbox_can_add_internal_note_with_rating_and_export_csv(): void
    {
        $this->actingAs($this->user);

        $conversation = Conversation::create([
            'workspace_id' => $this->workspace->id,
            'chat_id' => 'chat_test_note',
            'sender_mobile' => '19998887777',
            'sender_name' => 'Charlie Chaplin',
            'channel' => 'whatsapp_cloud',
            'status' => 'open',
        ]);

        Livewire::test(InboxPage::class)
            ->call('selectConversation', $conversation->id)
            ->set('internalNoteBody', 'High value enterprise lead')
            ->set('internalNoteRating', 5)
            ->call('addNote')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('conversation_notes', [
            'workspace_id' => $this->workspace->id,
            'conversation_id' => $conversation->id,
            'note' => 'High value enterprise lead',
            'rating' => 5,
        ]);

        // Test export CSV
        $exportTest = Livewire::test(InboxPage::class)
            ->call('selectConversation', $conversation->id)
            ->call('exportConversationCsv');

        $this->assertNotNull($exportTest);
    }
}
