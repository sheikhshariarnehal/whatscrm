<?php

namespace App\Services\Automation;

use App\Models\ChatbotRule;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Flow;
use App\Models\FlowSession;
use App\Models\Message;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Support\Facades\Log;

class FlowExecutionService
{
    /**
     * Evaluate incoming message against chatbot rules and active flows.
     */
    public function handleIncomingMessage(Conversation $conversation, Message $message, Contact $contact): void
    {
        $text = strtolower(trim($message->content ?? ''));
        if (empty($text)) {
            return;
        }

        $workspaceId = $conversation->workspace_id;

        // 1. Check if there is an active running flow session for this conversation
        $activeSession = FlowSession::where('conversation_id', $conversation->id)
            ->where('status', 'running')
            ->first();

        if ($activeSession) {
            $this->advanceFlowSession($activeSession, $text, $conversation);
            return;
        }

        // 2. Check keyword triggers for visual flows
        $flow = Flow::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->where('trigger_type', 'keyword')
            ->get()
            ->first(function ($f) use ($text) {
                $keywords = array_map('trim', explode(',', strtolower($f->trigger_keywords ?? '')));
                foreach ($keywords as $kw) {
                    if (!empty($kw) && str_contains($text, $kw)) {
                        return true;
                    }
                }
                return false;
            });

        if ($flow) {
            $this->startFlow($flow, $conversation, $contact);
            return;
        }

        // 3. Check simple keyword chatbot rules
        $rules = ChatbotRule::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->get();

        foreach ($rules as $rule) {
            if ($this->matchesRule($rule, $text)) {
                $this->executeRule($rule, $conversation, $contact);
                return;
            }
        }
    }

    /**
     * Check if text matches chatbot rule criteria.
     */
    protected function matchesRule(ChatbotRule $rule, string $text): bool
    {
        $keywords = array_map('trim', explode(',', strtolower($rule->keywords)));

        foreach ($keywords as $kw) {
            if (empty($kw)) continue;

            if ($rule->match_type === 'exact' && $text === $kw) {
                return true;
            } elseif ($rule->match_type === 'starts_with' && str_starts_with($text, $kw)) {
                return true;
            } elseif ($rule->match_type === 'contains' && str_contains($text, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Execute matched chatbot rule.
     */
    protected function executeRule(ChatbotRule $rule, Conversation $conversation, Contact $contact): void
    {
        $replyContent = $rule->reply_content;
        $replyText = is_array($replyContent) ? ($replyContent['text'] ?? '') : (string) $replyContent;

        // Dynamic interpolation
        $replyText = str_replace(
            ['{{name}}', '{{phone}}'],
            [$contact->name ?? 'there', $contact->phone],
            $replyText
        );

        if (empty($replyText)) {
            return;
        }

        // Save outbound message to conversation
        $botMessage = Message::create([
            'workspace_id' => $conversation->workspace_id,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'content' => $replyText,
            'status' => 'sent',
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'last_message' => $replyText,
        ]);

        // Send via Meta Cloud API if credential connected
        $cloudService = CloudApiService::forWorkspace($conversation->workspace_id);
        if ($cloudService) {
            $cloudService->sendTextMessage($contact->phone, $replyText);
        }
    }

    /**
     * Start a flow session.
     */
    protected function startFlow(Flow $flow, Conversation $conversation, Contact $contact): void
    {
        $flow->increment('execution_count');

        $session = FlowSession::create([
            'workspace_id' => $conversation->workspace_id,
            'flow_id' => $flow->id,
            'conversation_id' => $conversation->id,
            'contact_id' => $contact->id,
            'current_node_id' => 'start_node',
            'session_data' => ['step' => 1],
            'status' => 'running',
        ]);

        $flowData = $flow->flow_data ?? [];
        $firstStepText = $flowData['welcome_message'] ?? "Hello {$contact->name}! How can our automated assistant help you today?";

        Message::create([
            'workspace_id' => $conversation->workspace_id,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'content' => $firstStepText,
            'status' => 'sent',
        ]);

        $cloudService = CloudApiService::forWorkspace($conversation->workspace_id);
        if ($cloudService) {
            $cloudService->sendTextMessage($contact->phone, $firstStepText);
        }
    }

    /**
     * Advance existing flow session.
     */
    protected function advanceFlowSession(FlowSession $session, string $input, Conversation $conversation): void
    {
        $session->update([
            'status' => 'completed',
        ]);

        $reply = "Thank you! An agent has been assigned to your request and will follow up shortly.";
        Message::create([
            'workspace_id' => $conversation->workspace_id,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'content' => $reply,
            'status' => 'sent',
        ]);

        $contact = $conversation->contact;
        $cloudService = CloudApiService::forWorkspace($conversation->workspace_id);
        if ($cloudService && $contact) {
            $cloudService->sendTextMessage($contact->phone, $reply);
        }
    }
}
