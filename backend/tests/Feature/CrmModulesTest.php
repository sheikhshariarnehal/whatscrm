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
}
