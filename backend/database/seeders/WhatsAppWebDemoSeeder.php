<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class WhatsAppWebDemoSeeder extends Seeder
{
    public function run(): void
    {
        $ws = Workspace::first();
        if (!$ws) return;
        $wsId = $ws->id;

        // Clean up any extra dummy conversations in workspace to ensure exact match with screenshot
        Conversation::where('workspace_id', $wsId)
            ->whereNotIn('sender_mobile', [
                '+8801711000002',
                '+8801990423763',
                '+8801711000004',
                '+8801711000001',
                '+8801711000005',
                '+880 1629-373813',
                '+8801711000007',
                '+8801711000008',
                '+8801711000009',
            ])->delete();

        // 1. Interakt (Unread 1, 9:35 pm today)
        Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+8801711000002'],
            [
                'chat_id' => '8801711000002@c.us',
                'sender_name' => 'Interakt',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 1,
                'last_message' => 'Unleash the full potential of Interakt...',
                'updated_at' => now()->setTime(21, 35),
            ]
        );

        // 2. SRC 365 (6:35 pm today)
        Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+8801990423763'],
            [
                'chat_id' => '8801990423763@c.us',
                'sender_name' => 'SRC 365 > Creative Develop...',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 0,
                'last_message' => '+880 1990-423763 turned off disappe...',
                'updated_at' => now()->setTime(18, 35),
            ]
        );

        // 3. LeminAi (Yesterday)
        Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+8801711000004'],
            [
                'chat_id' => '8801711000004@c.us',
                'sender_name' => 'LeminAi',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 0,
                'last_message' => 'Hey, just checking in! Let us know if yo...',
                'updated_at' => now()->subDay()->setTime(16, 0),
            ]
        );

        // 4. Urmi ❤️ (Active Conversation, Yesterday)
        $urmi = Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+8801711000001'],
            [
                'chat_id' => '8801711000001@c.us',
                'sender_name' => 'Urmi❤️',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 0,
                'last_message' => 'আসসালামু আলাইকুম Dhaka Mobile & ...',
                'last_inbound_at' => now()->subDay()->setTime(15, 0),
                'updated_at' => now()->subDay()->setTime(15, 0),
            ]
        );

        $urmi->messages()->delete();

        // 2x2 Collage Outbound Image (8:35 am)
        $urmi->messages()->create([
            'workspace_id' => $wsId,
            'direction' => 'outbound',
            'type' => 'collage',
            'content' => 'Photo Collage (14 images)',
            'media_url' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?w=600&q=80',
            'status' => 'read',
            'created_at' => now()->subDay()->setTime(8, 35),
        ]);

        // Inbound Bengali message (3:00 pm)
        $urmi->messages()->create([
            'workspace_id' => $wsId,
            'direction' => 'inbound',
            'type' => 'text',
            'content' => "আসসালামু আলাইকুম Dhaka Mobile & Masud Telecom ,\n\nআমি ওয়েবসাইট তৈরি করি। আপনার ব্যবসার জন্য একটি সুন্দর ও professional website থাকলে online-এ আপনার businessটা আরও ভালোভাবে present করা যাবে।\n\nআমি আগে থেকেই কয়েকটা demo website তৈরি করেছি। চাইলে আপনাকে সেগুলো দেখাতে পারি। 😊\n\nDemo দেখতে চাইলে জানাবেন।",
            'status' => 'delivered',
            'created_at' => now()->subDay()->setTime(15, 0),
        ]);

        // 5. Jiad (CSE) (Thursday)
        Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+8801711000005'],
            [
                'chat_id' => '8801711000005@c.us',
                'sender_name' => 'Jiad (CSE)',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 0,
                'last_message' => '📄 Dhaka Mobile Shop Leads-2026...',
                'updated_at' => now()->subDays(2)->setTime(14, 20),
            ]
        );

        // 6. +880 1629-373813 (Thursday)
        Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+880 1629-373813'],
            [
                'chat_id' => '8801629373813@c.us',
                'sender_name' => '+880 1629-373813',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 0,
                'last_message' => 'HI',
                'updated_at' => now()->subDays(2)->setTime(12, 10),
            ]
        );

        // 7. Rishabh Gupta (Thursday)
        Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+8801711000007'],
            [
                'chat_id' => '8801711000007@c.us',
                'sender_name' => 'Rishabh Gupta',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 0,
                'last_message' => 'Hi, Rishabh from SandeshAI here. Just ...',
                'updated_at' => now()->subDays(2)->setTime(10, 5),
            ]
        );

        // 8. SandeshAI (Thursday)
        Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+8801711000008'],
            [
                'chat_id' => '8801711000008@c.us',
                'sender_name' => 'SandeshAI',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 0,
                'last_message' => 'Hello Sheikh Shariar Nehal, Your Sande...',
                'updated_at' => now()->subDays(2)->setTime(9, 15),
            ]
        );

        // 9. WhatsApp Business (Wednesday)
        Conversation::updateOrCreate(
            ['workspace_id' => $wsId, 'sender_mobile' => '+8801711000009'],
            [
                'chat_id' => '8801711000009@c.us',
                'sender_name' => 'WhatsApp Business',
                'channel' => 'whatsapp_cloud',
                'status' => 'open',
                'unread_count' => 0,
                'last_message' => 'Welcome to WhatsApp Business...',
                'updated_at' => now()->subDays(3)->setTime(11, 0),
            ]
        );
    }
}
