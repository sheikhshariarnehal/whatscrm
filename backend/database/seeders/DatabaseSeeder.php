<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\ChatbotRule;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\ConversationNote;
use App\Models\Flow;
use App\Models\Message;
use App\Models\Plan;
use App\Models\Phonebook;
use App\Models\QuickReply;
use App\Models\Tag;
use App\Models\User;
use App\Models\WebhookEndpoint;
use App\Models\WebhookLog;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SuperAdmin for Filament (/admin)
        $this->call(AdminSeeder::class);

        // 2. Plans
        $proPlan = Plan::firstOrCreate(
            ['slug' => 'pro-tier'],
            [
                'name' => 'Pro Business',
                'description' => 'Unlimited contacts, multi-agent inbox, and flow automations',
                'price_monthly' => 49.00,
                'price_yearly' => 490.00,
                'currency' => 'USD',
                'features' => ['cloud_api', 'inbox', 'kanban', 'campaigns', 'flows', 'team_agents'],
                'limits' => [
                    'messages_per_month' => 50000,
                    'contacts' => 10000,
                    'seats' => 5,
                ],
                'is_active' => true,
            ]
        );

        Plan::firstOrCreate(
            ['slug' => 'enterprise-tier'],
            [
                'name' => 'Enterprise Suite',
                'description' => 'Dedicated throughput, custom AI agents, and priority SLA support',
                'price_monthly' => 149.00,
                'price_yearly' => 1490.00,
                'currency' => 'USD',
                'features' => ['cloud_api', 'inbox', 'kanban', 'campaigns', 'flows', 'team_agents', 'unlimited_api'],
                'limits' => [
                    'messages_per_month' => 200000,
                    'contacts' => 50000,
                    'seats' => 20,
                ],
                'is_active' => true,
            ]
        );

        // 3. Demo User 1 & Workspace 1 (Acme Corp)
        $user1 = User::firstOrCreate(
            ['email' => 'demo@whatscrm.com'],
            [
                'name' => 'John Doe',
                'phone' => '+1234567890',
                'api_key' => Str::random(32),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create API Token for John Doe
        if ($user1->tokens()->count() === 0) {
            $user1->createToken('Production Webhook Token');
        }

        $workspace1 = Workspace::firstOrCreate(
            ['slug' => 'acme-corp'],
            [
                'name' => 'Acme Corp',
                'timezone' => 'America/New_York',
                'plan_id' => $proPlan->id,
                'plan_expires_at' => now()->addYear(),
                'is_on_trial' => false,
                'settings' => [
                    'country_code' => '+1',
                    'primary_color' => '#2a85ff',
                    'notifications' => [
                        'sound' => true,
                        'browser_push' => true,
                        'campaign_digest' => true,
                    ],
                    'ai' => [
                        'provider' => 'gemini',
                        'model' => 'gemini-2.5-flash',
                        'system_prompt' => 'You are an intelligent customer support assistant for Acme Corp on WhatsApp. Answer queries politely and concisely.',
                        'knowledge_base' => 'Business hours: 9 AM to 6 PM Mon-Fri. Delivery within 24-48 hours nationwide. 30-day money back guarantee.',
                        'temperature' => 0.7,
                    ],
                ],
            ]
        );

        $member1 = WorkspaceMember::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'user_id' => $user1->id],
            [
                'role' => 'owner',
                'permissions' => ['*'],
                'is_active' => true,
            ]
        );

        // Additional Agent in Acme Corp
        $agentUser = User::firstOrCreate(
            ['email' => 'sarah.agent@acme.com'],
            [
                'name' => 'Sarah Miller',
                'phone' => '+1555009988',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $agentMember = WorkspaceMember::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'user_id' => $agentUser->id],
            [
                'role' => 'agent',
                'permissions' => [
                    'can_view_all_chats' => false,
                    'can_send_broadcasts' => false,
                ],
                'is_active' => true,
            ]
        );

        // Acme Corp default Tags
        $leadTag = Tag::firstOrCreate(['workspace_id' => $workspace1->id, 'title' => 'New Lead'], ['hex_color' => '#2a85ff', 'show_on_kanban' => true]);
        $hotTag = Tag::firstOrCreate(['workspace_id' => $workspace1->id, 'title' => 'Hot Prospect'], ['hex_color' => '#ff6a55', 'show_on_kanban' => true]);
        $customerTag = Tag::firstOrCreate(['workspace_id' => $workspace1->id, 'title' => 'Won Customer'], ['hex_color' => '#10b981', 'show_on_kanban' => true]);

        // Acme Corp default Phonebook
        $phonebook1 = Phonebook::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'name' => 'Marketing Leads']
        );

        // Acme Corp Contacts
        $contact1 = Contact::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'mobile' => '+15551234567'],
            [
                'phonebook_id' => $phonebook1->id,
                'name' => 'Sarah Connor',
                'email' => 'sarah@cyberdyne.io',
                'source' => 'whatsapp_inbound',
                'custom_fields' => ['company' => 'Resistance LLC', 'interest' => 'Enterprise Plan'],
            ]
        );

        $contact2 = Contact::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'mobile' => '+15559876543'],
            [
                'phonebook_id' => $phonebook1->id,
                'name' => 'Tony Stark',
                'email' => 'tony@stark.com',
                'source' => 'campaign',
                'custom_fields' => ['company' => 'Stark Industries', 'budget' => '$100k'],
            ]
        );

        // Conversations for Inbox & Kanban
        $conv1 = Conversation::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'chat_id' => '+15551234567'],
            [
                'contact_id' => $contact1->id,
                'channel' => 'whatsapp_cloud',
                'sender_name' => 'Sarah Connor',
                'sender_mobile' => '+15551234567',
                'last_message' => 'Can you send over the pricing guide for the enterprise tier?',
                'last_message_at' => now()->subMinutes(12),
                'unread_count' => 1,
                'status' => 'open',
                'assigned_member_id' => $agentMember->id,
                'kanban_order' => 1,
            ]
        );

        $conv2 = Conversation::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'chat_id' => '+15559876543'],
            [
                'contact_id' => $contact2->id,
                'channel' => 'whatsapp_cloud',
                'sender_name' => 'Tony Stark',
                'sender_mobile' => '+15559876543',
                'last_message' => 'The demo looks solid. Let us finalize the contract terms next Monday.',
                'last_message_at' => now()->subHours(2),
                'unread_count' => 0,
                'status' => 'open',
                'assigned_member_id' => $member1->id,
                'kanban_order' => 2,
            ]
        );

        // Conversation Notes
        ConversationNote::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'conversation_id' => $conv1->id, 'user_id' => $user1->id],
            ['note' => 'Client expressed interest in upgrading to 50 agent seats by next quarter.']
        );

        // Messages for Conversation 1
        Message::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'conversation_id' => $conv1->id, 'content' => 'Hi, I need information about your WhatsApp CRM solution.'],
            [
                'direction' => 'inbound',
                'type' => 'text',
                'status' => 'delivered',
                'created_at' => now()->subMinutes(30),
            ]
        );

        Message::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'conversation_id' => $conv1->id, 'content' => 'Hello Sarah! Thank you for reaching out. We offer multi-agent live chat, broadcasts, and AI automations.'],
            [
                'direction' => 'outbound',
                'type' => 'text',
                'status' => 'read',
                'sent_by_user_id' => $agentUser->id,
                'created_at' => now()->subMinutes(25),
            ]
        );

        Message::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'conversation_id' => $conv1->id, 'content' => 'Can you send over the pricing guide for the enterprise tier?'],
            [
                'direction' => 'inbound',
                'type' => 'text',
                'status' => 'delivered',
                'created_at' => now()->subMinutes(12),
            ]
        );

        // Messages for Conversation 2
        Message::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'conversation_id' => $conv2->id, 'content' => 'Hello Tony, sending you the updated SLA documents for review.'],
            [
                'direction' => 'outbound',
                'type' => 'text',
                'status' => 'read',
                'sent_by_user_id' => $user1->id,
                'created_at' => now()->subHours(3),
            ]
        );

        Message::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'conversation_id' => $conv2->id, 'content' => 'The demo looks solid. Let us finalize the contract terms next Monday.'],
            [
                'direction' => 'inbound',
                'type' => 'text',
                'status' => 'delivered',
                'created_at' => now()->subHours(2),
            ]
        );

        // 4. Sample Campaign for Acme Corp
        $campaign = Campaign::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'name' => 'Spring VIP Announcement 2026'],
            [
                'type' => 'cloud_template',
                'template_name' => 'sample_promo_2026',
                'template_language' => 'en',
                'template_variables' => ['1' => 'name', '2' => 'phone'],
                'target_type' => 'all',
                'status' => 'completed',
                'scheduled_at' => now()->subDays(1),
                'started_at' => now()->subDays(1),
                'completed_at' => now()->subDays(1)->addMinutes(5),
                'total_recipients' => 2,
                'sent_count' => 2,
                'delivered_count' => 2,
                'read_count' => 2,
                'failed_count' => 0,
            ]
        );

        CampaignLog::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'campaign_id' => $campaign->id, 'phone' => '+15551234567'],
            [
                'contact_id' => $contact1->id,
                'variables_sent' => ['1' => 'Sarah Connor', '2' => '+15551234567'],
                'status' => 'read',
                'external_message_id' => 'wam_demo_001',
            ]
        );

        CampaignLog::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'campaign_id' => $campaign->id, 'phone' => '+15559876543'],
            [
                'contact_id' => $contact2->id,
                'variables_sent' => ['1' => 'Tony Stark', '2' => '+15559876543'],
                'status' => 'read',
                'external_message_id' => 'wam_demo_002',
            ]
        );

        // 5. Sample Visual Flow
        Flow::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'name' => 'Lead Qualification & Consultation Tree'],
            [
                'trigger_type' => 'keyword',
                'trigger_keywords' => 'demo, pricing, quote, consult',
                'flow_data' => [
                    'welcome_message' => 'Hello {{name}}! Welcome to Acme Corp. Which plan are you looking to explore today?',
                ],
                'is_active' => true,
                'execution_count' => 48,
            ]
        );

        // 6. Sample Chatbot Rules
        ChatbotRule::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'keywords' => 'hours, opening time, schedule'],
            [
                'match_type' => 'contains',
                'reply_type' => 'text',
                'reply_content' => ['text' => 'Hello {{name}}! Our customer support operates Monday to Friday, 9:00 AM to 6:00 PM EST.'],
                'priority' => 10,
                'is_active' => true,
            ]
        );

        ChatbotRule::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'keywords' => 'pricing, cost, plans'],
            [
                'match_type' => 'contains',
                'reply_type' => 'text',
                'reply_content' => ['text' => 'Hi {{name}}! Our Pro Business plan starts at $49/mo. Check our pricing overview at https://whatscrm.com/pricing'],
                'priority' => 20,
                'is_active' => true,
            ]
        );

        // 7. Quick Replies
        QuickReply::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'shortcut' => '/pricing'],
            [
                'message' => 'Our Pro plan is $49/month including official WhatsApp Cloud API, unlimited contacts, and multi-agent inbox.',
                'category' => 'Sales',
            ]
        );

        QuickReply::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'shortcut' => '/onboarding'],
            [
                'message' => 'Welcome aboard! Our customer success team will assist you with Meta Business verification and number pairing.',
                'category' => 'Support',
            ]
        );

        // 8. Sample Webhook Endpoint
        $webhook = WebhookEndpoint::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'name' => 'Shopify Order Sync'],
            [
                'url' => 'https://api.acmeshop.com/webhooks/whatsapp',
                'secret' => Str::random(32),
                'events' => ['message.received', 'message.status', 'contact.created'],
                'is_active' => true,
            ]
        );

        WebhookLog::firstOrCreate(
            ['workspace_id' => $workspace1->id, 'event' => 'ping.test'],
            [
                'webhook_endpoint_id' => $webhook->id,
                'direction' => 'outbound',
                'payload' => ['event' => 'ping.test', 'status' => 'verified', 'timestamp' => now()->toIso8601String()],
                'response_status' => 200,
                'response_body' => '{"received": true}',
            ]
        );

        // 9. Demo User 2 & Workspace 2 (Beta Agency - For testing tenant isolation)
        $user2 = User::firstOrCreate(
            ['email' => 'beta@whatscrm.com'],
            [
                'name' => 'Alice Smith',
                'phone' => '+1987654321',
                'api_key' => Str::random(32),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $workspace2 = Workspace::firstOrCreate(
            ['slug' => 'beta-agency'],
            [
                'name' => 'Beta Agency',
                'timezone' => 'America/New_York',
                'plan_id' => $proPlan->id,
                'plan_expires_at' => now()->addMonths(6),
                'is_on_trial' => false,
            ]
        );

        WorkspaceMember::firstOrCreate(
            ['workspace_id' => $workspace2->id, 'user_id' => $user2->id],
            [
                'role' => 'owner',
                'permissions' => ['*'],
                'is_active' => true,
            ]
        );

        Contact::firstOrCreate(
            ['workspace_id' => $workspace2->id, 'mobile' => '+15550001111'],
            [
                'name' => 'Bruce Wayne',
                'email' => 'bruce@wayne.com',
                'source' => 'manual',
                'custom_fields' => ['city' => 'Gotham'],
            ]
        );
    }
}
