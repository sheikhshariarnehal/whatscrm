<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Instance;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaileysWebhookController extends Controller
{
    /**
     * Handle incoming WhatsApp messages forwarded from the Baileys Node.js service.
     */
    public function handle(Request $request): JsonResponse
    {
        $secret = $request->header('X-Internal-Secret');
        $expectedSecret = config('services.baileys.secret', '');

        if (!empty($expectedSecret) && $secret !== $expectedSecret) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $uniqueId  = $request->input('uniqueId');
        $fromPhone = $request->input('from');
        $name      = $request->input('name') ?: $fromPhone;
        $text      = $request->input('text') ?: '';
        $messageId = $request->input('messageId');

        if (!$fromPhone) {
            return response()->json(['success' => false, 'message' => 'Sender phone required'], 400);
        }

        // Determine workspace from instance
        $instance = Instance::where('uniqueId', $uniqueId)->first();
        $workspaceId = $instance ? (int) $instance->uid : (int) ($request->input('uid') ?: 1);

        try {
            // Find or create Contact (mobile column)
            $contact = Contact::firstOrCreate(
                ['workspace_id' => $workspaceId, 'mobile' => $fromPhone],
                ['name' => $name]
            );

            // Find or create Conversation
            $conversation = Conversation::firstOrCreate(
                [
                    'workspace_id' => $workspaceId,
                    'chat_id'      => $fromPhone,
                ],
                [
                    'contact_id'    => $contact->id,
                    'channel'       => 'baileys',
                    'instance_id'   => $uniqueId,
                    'sender_name'   => $name,
                    'sender_mobile' => $fromPhone,
                    'status'        => 'open',
                ]
            );

            // Create Inbound Message
            $msg = Message::create([
                'workspace_id'    => $workspaceId,
                'conversation_id' => $conversation->id,
                'direction'       => 'inbound',
                'type'            => 'text',
                'content'         => $text,
                'status'          => 'delivered',
                'channel'         => 'baileys',
                'external_id'     => $messageId,
            ]);

            // Update conversation stats
            $conversation->update([
                'last_message'    => $text,
                'last_message_at' => now(),
                'unread_count'    => $conversation->unread_count + 1,
            ]);

            return response()->json(['success' => true, 'message_id' => $msg->id]);
        } catch (\Throwable $e) {
            Log::error("[BaileysWebhookController] Failed to record incoming message: {$e->getMessage()}");
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
