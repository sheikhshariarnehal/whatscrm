<?php

namespace App\Services\Automation;

use App\Jobs\ProcessDelayedFlowNode;
use App\Models\BotBinding;
use App\Models\ChatbotRule;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Flow;
use App\Models\FlowSession;
use App\Models\Message;
use App\Models\Tag;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlowExecutionService
{
    const MAX_NODE_VISITS = 3;
    const MAX_ITERATIONS = 30;

    /**
     * Intercept and evaluate incoming WhatsApp / Webhook messages against active bots.
     */
    public function handleIncomingMessage(
        Conversation $conversation,
        Message $message,
        Contact $contact,
        string $channel = 'meta',
        ?string $channelId = null
    ): void {
        $text = trim($message->content ?? '');
        $workspaceId = $conversation->workspace_id;

        // 1. Check if an active session is currently running for this conversation
        $activeSession = FlowSession::where('conversation_id', $conversation->id)
            ->whereIn('status', ['running', 'waiting_input'])
            ->latest()
            ->first();

        if ($activeSession) {
            // Check if auto-reply is currently muted for a human agent
            if ($activeSession->auto_reply_disabled_until && now()->lt($activeSession->auto_reply_disabled_until)) {
                Log::info("[FlowEngine] Auto-reply is paused for conversation {$conversation->id} until {$activeSession->auto_reply_disabled_until}");
                return;
            }

            $this->advanceFlowSession($activeSession, $text);
            return;
        }

        // 2. Check Channel Bot Bindings (Multi-Channel routing matrix)
        $bindingQuery = BotBinding::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->where('channel', $channel);

        if ($channelId) {
            $binding = (clone $bindingQuery)->where('origin_id', $channelId)->first() 
                    ?: (clone $bindingQuery)->where('origin_id', 'META_CLOUD_API')->first()
                    ?: (clone $bindingQuery)->where('origin_id', 'ALL_DEVICES')->first();
        } else {
            $binding = $bindingQuery->first();
        }

        if ($binding && $binding->flow && $binding->flow->is_active) {
            Log::info("[FlowEngine] Inbound message matched BotBinding #{$binding->id} ('{$binding->title}') -> Triggering Flow #{$binding->flow_id}");
            $this->startFlowSession($binding->flow, $conversation, $contact, $channel, $channelId, $text);
            return;
        }

        // 3. Check Keyword Triggers on Active Visual Flows
        $lowerText = strtolower($text);
        $flow = Flow::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->where('trigger_type', 'keyword')
            ->get()
            ->first(function ($f) use ($lowerText) {
                $keywords = array_map('trim', explode(',', strtolower($f->trigger_keywords ?? '')));
                foreach ($keywords as $kw) {
                    if (!empty($kw) && str_contains($lowerText, $kw)) {
                        return true;
                    }
                }
                return false;
            });

        if ($flow) {
            Log::info("[FlowEngine] Inbound message matched keyword trigger -> Triggering Flow #{$flow->id}");
            $this->startFlowSession($flow, $conversation, $contact, $channel, $channelId, $text);
            return;
        }

        // 4. Check Keyword Chatbot Rules
        $rules = ChatbotRule::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->get();

        foreach ($rules as $rule) {
            if ($this->matchesRule($rule, $lowerText)) {
                $this->executeChatbotRule($rule, $conversation, $contact, $channel);
                return;
            }
        }
    }

    /**
     * Start a new FlowSession and execute the initial node graph.
     */
    public function startFlowSession(
        Flow $flow,
        Conversation $conversation,
        Contact $contact,
        string $channel = 'meta',
        ?string $channelId = null,
        ?string $initialInput = null
    ): FlowSession {
        $flow->increment('execution_count');

        // Clean up any stale sessions
        FlowSession::where('conversation_id', $conversation->id)->delete();

        $session = FlowSession::create([
            'workspace_id' => $conversation->workspace_id,
            'flow_id' => $flow->id,
            'conversation_id' => $conversation->id,
            'contact_id' => $contact->id,
            'channel_type' => $channel,
            'channel_id' => $channelId,
            'current_node_id' => 'initialNode',
            'session_data' => ['started_at' => now()->toIso8601String()],
            'variables' => [
                'name' => $contact->name ?? 'Customer',
                'phone' => $contact->phone,
                'first_name' => explode(' ', $contact->name ?? 'Friend')[0],
            ],
            'visited_nodes' => [],
            'status' => 'running',
        ]);

        $this->executeNodeSequence($session, 'initialNode', $initialInput);

        return $session;
    }

    /**
     * Advance a waiting flow session with incoming customer response.
     */
    protected function advanceFlowSession(FlowSession $session, string $userInput): void
    {
        $flowData = $session->flow->flow_data ?? [];
        $nodes = $flowData['nodes'] ?? [];
        $edges = $flowData['edges'] ?? [];

        $currentNodeId = $session->current_node_id;
        $currentNode = collect($nodes)->firstWhere('id', $currentNodeId);

        if (!$currentNode) {
            $session->update(['status' => 'terminated']);
            return;
        }

        // If current node is a RESPONSE_SAVER or collect_input, save the input into variable
        $type = strtolower($currentNode['type'] ?? '');
        if ($type === 'collect_input' || $currentNode['type'] === 'RESPONSE_SAVER') {
            $varName = $currentNode['data']['var_key'] ?? ($currentNode['data']['variableName'] ?? 'last_input');
            $vars = $session->variables ?? [];
            $vars[$varName] = $userInput;
            $session->variables = $vars;
            $session->save();
        }

        // Evaluate target node
        $targetNodeId = null;
        if ($type === 'condition' || $currentNode['type'] === 'CONDITION') {
            $targetNodeId = $this->evaluateConditionTarget($currentNode, $userInput, $session->variables ?? [], $edges);
        } elseif ($type === 'send_buttons' || $currentNode['type'] === 'QUICK_REPLY') {
            // Find which button matched the user response
            $buttons = $currentNode['data']['buttons'] ?? ($currentNode['data']['replyButtons'] ?? []);
            $matchedBtnIndex = null;
            $lowerInput = mb_strtolower(trim($userInput));
            foreach ($buttons as $idx => $btn) {
                $bTitle = mb_strtolower(trim($btn['title'] ?? ($btn['text'] ?? '')));
                $bId = mb_strtolower(trim($btn['id'] ?? ($btn['reply_id'] ?? ('btn_' . $idx))));
                if ($lowerInput === $bTitle || $lowerInput === $bId || $userInput === (string)($idx + 1)) {
                    $matchedBtnIndex = $idx;
                    break;
                }
            }

            if ($matchedBtnIndex !== null) {
                $btnHandle = 'btn_' . $matchedBtnIndex;
                $edge = collect($edges)->first(function ($e) use ($currentNodeId, $btnHandle) {
                    return $e['source'] === $currentNodeId && ($e['sourceHandle'] ?? '') === $btnHandle;
                });
                if ($edge) {
                    $targetNodeId = $edge['target'] ?? null;
                }
            }

            if (!$targetNodeId) {
                // Fallback to default or any edge
                $defaultEdge = collect($edges)->first(function ($e) use ($currentNodeId) {
                    return $e['source'] === $currentNodeId && in_array($e['sourceHandle'] ?? '', ['default', '', null]);
                }) ?: collect($edges)->firstWhere('source', $currentNodeId);
                $targetNodeId = $defaultEdge['target'] ?? null;
            }
        } elseif ($type === 'send_list' || $currentNode['type'] === 'INTERACTIVE_LIST') {
            $items = $currentNode['data']['items'] ?? [];
            $matchedItemIdx = null;
            $lowerInput = mb_strtolower(trim($userInput));
            foreach ($items as $idx => $item) {
                $iTitle = mb_strtolower(trim($item['title'] ?? ''));
                $iId = mb_strtolower(trim($item['id'] ?? ('item_' . $idx)));
                if ($lowerInput === $iTitle || $lowerInput === $iId) {
                    $matchedItemIdx = $idx;
                    break;
                }
            }

            if ($matchedItemIdx !== null) {
                $itemHandle = 'item_' . $matchedItemIdx;
                $edge = collect($edges)->first(function ($e) use ($currentNodeId, $itemHandle) {
                    return $e['source'] === $currentNodeId && ($e['sourceHandle'] ?? '') === $itemHandle;
                });
                if ($edge) {
                    $targetNodeId = $edge['target'] ?? null;
                }
            }

            if (!$targetNodeId) {
                $defaultEdge = collect($edges)->firstWhere('source', $currentNodeId);
                $targetNodeId = $defaultEdge['target'] ?? null;
            }
        } else {
            // Find normal outgoing edge
            $outgoingEdge = collect($edges)->firstWhere('source', $currentNodeId);
            $targetNodeId = $outgoingEdge['target'] ?? null;
        }

        if ($targetNodeId) {
            $this->executeNodeSequence($session, $targetNodeId, $userInput);
        } else {
            // End of flow
            $session->update(['status' => 'completed']);
        }
    }

    /**
     * Loop traversal: execute consecutive nodes until user input or delay is needed.
     */
    public function executeNodeSequence(FlowSession $session, string $startNodeId, ?string $userInput = null): void
    {
        $flow = $session->flow;
        $flowData = $flow->flow_data ?? [];
        $nodes = collect($flowData['nodes'] ?? []);
        $edges = collect($flowData['edges'] ?? []);

        $currentNodeId = $startNodeId;
        $iterationCount = 0;
        $visited = $session->visited_nodes ?? [];

        while ($currentNodeId && $iterationCount < self::MAX_ITERATIONS) {
            $iterationCount++;

            // Loop / Infinite visit guard
            $visits = ($visited[$currentNodeId] ?? 0) + 1;
            $visited[$currentNodeId] = $visits;
            $session->visited_nodes = $visited;

            if ($visits > self::MAX_NODE_VISITS) {
                Log::warning("[FlowEngine] Loop detected at node {$currentNodeId} in session {$session->id}. Terminating flow.");
                $session->update(['status' => 'terminated']);
                return;
            }

            $node = $nodes->firstWhere('id', $currentNodeId);
            if (!$node) {
                break;
            }

            $session->current_node_id = $currentNodeId;
            $session->save();

            // Execute the specific node action
            $result = $this->executeSingleNode($session, $node, $userInput);

            // Handle Flow Control
            if ($result['action'] === 'wait_input') {
                $session->update(['status' => 'waiting_input']);
                return;
            }

            if ($result['action'] === 'delay') {
                $seconds = $result['delay_seconds'] ?? 5;
                $targetId = $result['next_node_id'] ?? null;
                if ($targetId) {
                    ProcessDelayedFlowNode::dispatch($session->id, $targetId)->delay(now()->addSeconds($seconds));
                }
                $session->update(['status' => 'running']);
                return;
            }

            if ($result['action'] === 'terminate') {
                $session->update(['status' => 'terminated']);
                return;
            }

            // Resolve next node ID
            if (isset($result['next_node_id'])) {
                $currentNodeId = $result['next_node_id'];
            } else {
                $edge = $edges->firstWhere('source', $currentNodeId);
                $currentNodeId = $edge['target'] ?? null;
            }
        }

        if (!$currentNodeId) {
            $session->update(['status' => 'completed']);
        }
    }

    /**
     * Dispatch node action according to its type.
     */
    protected function executeSingleNode(FlowSession $session, array $node, ?string $userInput): array
    {
        $rawType = $node['type'] ?? 'send_message';
        $type = strtolower($rawType);
        $data = $node['data'] ?? [];
        $vars = $session->variables ?? [];
        $conversation = $session->conversation;
        $contact = $session->contact;

        switch ($type) {
            case 'start':
            case 'initial':
                return ['action' => 'continue'];

            case 'send_message':
                $rawBody = $data['content']['text']['body'] ?? ($data['text'] ?? 'Hello!');
                $interpolated = $this->interpolateVariables($rawBody, $vars);
                $this->sendOutboundMessage($conversation, $contact, $interpolated, $session->channel_type);
                $expectsReply = $data['moveToNextNode'] ?? false;
                if (!$expectsReply) {
                    return ['action' => 'wait_input'];
                }
                return ['action' => 'continue'];

            case 'send_buttons':
            case 'quick_reply':
                $rawBody = $data['content']['text']['body'] ?? ($data['text'] ?? ($data['body_text'] ?? 'Please choose an option:'));
                $interpolated = $this->interpolateVariables($rawBody, $vars);
                $buttons = $data['buttons'] ?? ($data['replyButtons'] ?? [
                    ['id' => 'btn_0', 'title' => 'Yes'],
                    ['id' => 'btn_1', 'title' => 'No'],
                ]);
                $header = $data['header'] ?? null;
                $footer = $data['footer'] ?? null;
                $this->sendOutboundButtons($conversation, $contact, $interpolated, $buttons, $header, $footer, $session->channel_type);
                return ['action' => 'wait_input'];

            case 'send_list':
            case 'interactive_list':
                $rawBody = $data['content']['text']['body'] ?? ($data['text'] ?? 'Please select from the menu:');
                $interpolated = $this->interpolateVariables($rawBody, $vars);
                $buttonText = $data['button_text'] ?? ($data['buttonTitle'] ?? 'View Options');
                $sections = $data['sections'] ?? [];
                if (empty($sections) && !empty($data['items'])) {
                    $rows = [];
                    foreach ($data['items'] as $idx => $it) {
                        $rows[] = [
                            'id' => (string) ($it['id'] ?? ('item_' . $idx)),
                            'title' => mb_substr($it['title'] ?? ('Option ' . ($idx + 1)), 0, 24),
                            'description' => mb_substr($it['description'] ?? '', 0, 72),
                        ];
                    }
                    $sections = [['title' => 'Options', 'rows' => $rows]];
                }
                $this->sendOutboundList($conversation, $contact, $interpolated, $buttonText, $sections, $session->channel_type);
                return ['action' => 'wait_input'];

            case 'send_media':
                $mediaType = strtolower($data['mediaType'] ?? ($data['type'] ?? 'image'));
                $mediaUrl = $data['mediaUrl'] ?? ($data['url'] ?? '');
                $caption = $this->interpolateVariables($data['caption'] ?? '', $vars);
                if ($mediaUrl) {
                    $this->sendOutboundMedia($conversation, $contact, $mediaType, $mediaUrl, $caption, $session->channel_type);
                }
                return ['action' => 'continue'];

            case 'collect_input':
            case 'response_saver':
                $prompt = $data['prompt_text'] ?? ($data['prompt'] ?? ($data['text'] ?? ''));
                if (!empty($prompt)) {
                    $interpolated = $this->interpolateVariables($prompt, $vars);
                    $this->sendOutboundMessage($conversation, $contact, $interpolated, $session->channel_type);
                }
                return ['action' => 'wait_input'];

            case 'condition':
                $edges = collect($session->flow->flow_data['edges'] ?? []);
                $targetId = $this->evaluateConditionTarget($node, (string) ($userInput ?? ''), $vars, $edges);
                return ['action' => 'continue', 'next_node_id' => $targetId];

            case 'set_tag':
            case 'set_chat_label':
                $label = $data['label'] ?? ($data['tag'] ?? 'Lead');
                $tag = Tag::firstOrCreate([
                    'workspace_id' => $conversation->workspace_id,
                    'name' => $label,
                ]);
                $conversation->tags()->syncWithoutDetaching([$tag->id]);
                return ['action' => 'continue'];

            case 'handoff':
            case 'agent_transfer':
                $conversation->update(['status' => 'open']);
                $hours = (int) ($data['duration'] ?? 4);
                $session->auto_reply_disabled_until = now()->addHours($hours);
                $session->save();
                $reply = $data['message'] ?? "An agent will be with you shortly! Automated replies have been paused.";
                $this->sendOutboundMessage($conversation, $contact, $reply, $session->channel_type);
                return ['action' => 'terminate'];

            case 'end':
            case 'reset':
                return ['action' => 'terminate'];

            case 'delay':
                $seconds = (int) ($data['seconds'] ?? 5);
                $edges = collect($session->flow->flow_data['edges'] ?? []);
                $edge = $edges->firstWhere('source', $node['id']);
                return [
                    'action' => 'delay',
                    'delay_seconds' => $seconds,
                    'next_node_id' => $edge['target'] ?? null,
                ];

            case 'ai_assistant':
            case 'ai_transfer':
                $this->executeAiTransfer($data, $vars, $session, $userInput);
                return ['action' => 'wait_input'];

            case 'http_webhook':
            case 'make_request':
                $this->executeHttpRequest($data, $vars, $session);
                return ['action' => 'continue'];

            case 'disable_autoreply':
                $hours = (int) ($data['hours'] ?? 1);
                $session->auto_reply_disabled_until = now()->addHours($hours);
                $session->save();
                return ['action' => 'terminate'];

            default:
                return ['action' => 'continue'];
        }
    }

    /**
     * Evaluate condition branch.
     */
    protected function evaluateConditionTarget(array $node, string $input, array $vars, $edges): ?string
    {
        $conditions = $node['data']['conditions'] ?? [];
        $lowerInput = strtolower($input);

        foreach ($conditions as $cond) {
            $type = $cond['type'] ?? 'text_contains';
            $val = strtolower($cond['value'] ?? '');
            $targetHandle = $cond['targetHandle'] ?? ($cond['targetNodeId'] ?? 'match');

            $matched = false;
            if ($type === 'text_contains' && str_contains($lowerInput, $val)) {
                $matched = true;
            } elseif ($type === 'text_exact' && $lowerInput === $val) {
                $matched = true;
            } elseif ($type === 'starts_with' && str_starts_with($lowerInput, $val)) {
                $matched = true;
            }

            if ($matched) {
                $edge = collect($edges)->first(function ($e) use ($node, $targetHandle) {
                    return $e['source'] === $node['id'] && in_array($e['sourceHandle'] ?? '', [$targetHandle, 'match', 'true']);
                });
                if ($edge) {
                    return $edge['target'];
                }
            }
        }

        // Check fallback default handle edge (e.g. 'default' or 'false' port)
        $defaultHandleEdge = collect($edges)->first(function ($e) use ($node) {
            return $e['source'] === $node['id'] && in_array($e['sourceHandle'] ?? '', ['default', 'false']);
        });
        if ($defaultHandleEdge) {
            return $defaultHandleEdge['target'];
        }

        // Fallback to any edge from this node
        $defaultEdge = collect($edges)->firstWhere('source', $node['id']);
        return $defaultEdge['target'] ?? null;
    }

    /**
     * Send outbound message to database and channel provider (Meta / Baileys).
     */
    protected function sendOutboundMessage(Conversation $conversation, Contact $contact, string $text, string $channel = 'meta'): void
    {
        if (empty(trim($text))) {
            return;
        }

        $botMsg = Message::create([
            'workspace_id' => $conversation->workspace_id,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'content' => $text,
            'status' => 'sent',
            'channel' => $channel,
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'last_message' => $text,
        ]);

        // Dispatch via Meta Cloud API if credential connected
        if ($channel === 'meta') {
            $cloudService = CloudApiService::forWorkspace($conversation->workspace_id);
            if ($cloudService) {
                $cloudService->sendTextMessage($contact->phone, $text);
            }
        } elseif ($channel === 'qr') {
            // Baileys Node service integration
            try {
                $baileysUrl = config('services.baileys.url', 'http://127.0.0.1:3000');
                Http::timeout(5)->post("{$baileysUrl}/api/send-message", [
                    'instance_id' => $conversation->instance_id,
                    'phone' => $contact->phone,
                    'message' => $text,
                ]);
            } catch (\Throwable $e) {
                Log::warning("[FlowEngine] Baileys outbound dispatch skipped: " . $e->getMessage());
            }
        }
    }

    /**
     * Send interactive buttons to contact via Cloud API.
     */
    protected function sendOutboundButtons(Conversation $conversation, Contact $contact, string $text, array $buttons, ?string $header = null, ?string $footer = null, string $channel = 'meta'): void
    {
        $botMsg = Message::create([
            'workspace_id' => $conversation->workspace_id,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'interactive',
            'content' => $text,
            'metadata' => [
                'interactive_type' => 'button',
                'buttons' => $buttons,
                'header' => $header,
                'footer' => $footer,
            ],
            'status' => 'sent',
            'channel' => $channel,
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'last_message' => $text,
        ]);

        if ($channel === 'meta') {
            $cloudService = CloudApiService::forWorkspace($conversation->workspace_id);
            if ($cloudService) {
                $cloudService->sendInteractiveButtons($contact->phone, $text, $buttons, $header, $footer);
            }
        } else {
            // Text fallback for QR / Baileys channel
            $btnList = implode("\n", array_map(function($b, $i) {
                return ($i + 1) . '. ' . ($b['title'] ?? ($b['text'] ?? 'Option'));
            }, $buttons, array_keys($buttons)));
            $fallback = $text . "\n\n" . $btnList . "\n\n(Reply with option number or title)";
            $this->sendOutboundMessage($conversation, $contact, $fallback, $channel);
        }
    }

    /**
     * Send media to contact via Cloud API.
     */
    protected function sendOutboundMedia(Conversation $conversation, Contact $contact, string $mediaType, string $mediaUrl, ?string $caption = null, string $channel = 'meta'): void
    {
        Message::create([
            'workspace_id' => $conversation->workspace_id,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => $mediaType,
            'content' => $mediaUrl,
            'caption' => $caption,
            'status' => 'sent',
            'channel' => $channel,
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'last_message' => "[{$mediaType}] " . ($caption ?: ''),
        ]);

        if ($channel === 'meta') {
            $cloudService = CloudApiService::forWorkspace($conversation->workspace_id);
            if ($cloudService) {
                $cloudService->sendMediaMessage($contact->phone, $mediaType, $mediaUrl, $caption);
            }
        }
    }

    /**
     * Send interactive list to contact via Cloud API.
     */
    protected function sendOutboundList(Conversation $conversation, Contact $contact, string $text, string $buttonText, array $sections, string $channel = 'meta'): void
    {
        Message::create([
            'workspace_id' => $conversation->workspace_id,
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'interactive',
            'content' => $text,
            'metadata' => [
                'interactive_type' => 'list',
                'button_text' => $buttonText,
                'sections' => $sections,
            ],
            'status' => 'sent',
            'channel' => $channel,
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'last_message' => $text,
        ]);

        if ($channel === 'meta') {
            $cloudService = CloudApiService::forWorkspace($conversation->workspace_id);
            if ($cloudService) {
                $cloudService->sendInteractiveList($contact->phone, $text, $buttonText, $sections);
            }
        }
    }

    /**
     * Perform HTTP webhook request and extract response variables.
     */
    protected function executeHttpRequest(array $data, array &$vars, FlowSession $session): void
    {
        $url = $this->interpolateVariables($data['url'] ?? '', $vars);
        $method = strtoupper($data['method'] ?? 'GET');

        if (empty($url)) return;

        try {
            $response = Http::timeout(10)->send($method, $url);
            if ($response->successful()) {
                $json = $response->json();
                foreach ($data['variables'] ?? [] as $mapping) {
                    $key = $mapping['key'] ?? null;
                    $path = $mapping['value'] ?? null;
                    if ($key && $path) {
                        $extracted = data_get($json, $path);
                        if ($extracted) {
                            $vars[$key] = $extracted;
                        }
                    }
                }
                $session->variables = $vars;
                $session->save();
            }
        } catch (\Throwable $e) {
            Log::error("[FlowEngine] MAKE_REQUEST failed for {$url}: {$e->getMessage()}");
        }
    }

    /**
     * AI LLM Handover query.
     */
    protected function executeAiTransfer(array $data, array $vars, FlowSession $session, ?string $userInput): void
    {
        $workspace = $session->workspace ?? auth()->user()->currentWorkspace();
        $aiSettings = $workspace->settings['ai'] ?? [];
        $provider = $aiSettings['provider'] ?? 'gemini';
        $apiKey = $aiSettings['api_key'] ?? null;
        $systemPrompt = $data['systemPrompt'] ?? ($aiSettings['system_prompt'] ?? 'You are a helpful assistant.');
        $knowledgeBase = $aiSettings['knowledge_base'] ?? '';

        $reply = "Thank you for your message! Our automated assistant has noted your query.";

        if ($provider === 'gemini' && !empty($apiKey)) {
            try {
                $res = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(12)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                        'contents' => [
                            ['role' => 'user', 'parts' => [['text' => "Instructions: {$systemPrompt}\nFacts: {$knowledgeBase}\nCustomer: {$userInput}"]]],
                        ],
                    ]);
                if ($res->successful()) {
                    $reply = $res->json()['candidates'][0]['content']['parts'][0]['text'] ?? $reply;
                }
            } catch (\Throwable $e) {
                Log::warning("[FlowEngine] Gemini AI Transfer failed: {$e->getMessage()}");
            }
        }

        $this->sendOutboundMessage($session->conversation, $session->contact, $reply, $session->channel_type);
    }

    /**
     * Replace dynamic variables like {{{name}}}, {{{phone}}}, {{{variable}}}.
     */
    protected function interpolateVariables(string $template, array $vars): string
    {
        return preg_replace_callback('/\{\{\{?([a-zA-Z0-9_\.]+)\}?\}\}/', function ($matches) use ($vars) {
            $key = $matches[1];
            return (string) ($vars[$key] ?? $matches[0]);
        }, $template);
    }

    protected function matchesRule(ChatbotRule $rule, string $text): bool
    {
        $keywords = array_map('trim', explode(',', strtolower($rule->keywords)));
        foreach ($keywords as $kw) {
            if (empty($kw)) continue;
            if ($rule->match_type === 'exact' && $text === $kw) return true;
            if ($rule->match_type === 'starts_with' && str_starts_with($text, $kw)) return true;
            if ($rule->match_type === 'contains' && str_contains($text, $kw)) return true;
        }
        return false;
    }

    protected function executeChatbotRule(ChatbotRule $rule, Conversation $conversation, Contact $contact, string $channel): void
    {
        $replyContent = $rule->reply_content;
        $replyText = is_array($replyContent) ? ($replyContent['text'] ?? '') : (string) $replyContent;
        $replyText = str_replace(['{{name}}', '{{phone}}'], [$contact->name ?? 'there', $contact->phone], $replyText);

        $this->sendOutboundMessage($conversation, $contact, $replyText, $channel);
    }
}
