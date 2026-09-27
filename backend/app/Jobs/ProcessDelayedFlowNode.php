<?php

namespace App\Jobs;

use App\Models\Conversation;
use App\Models\FlowSession;
use App\Services\Automation\FlowExecutionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDelayedFlowNode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $sessionId;
    public string $targetNodeId;

    public function __construct(int $sessionId, string $targetNodeId)
    {
        $this->sessionId = $sessionId;
        $this->targetNodeId = $targetNodeId;
    }

    public function handle(FlowExecutionService $engine): void
    {
        $session = FlowSession::with(['flow', 'conversation', 'contact'])->find($this->sessionId);

        if (!$session || $session->status === 'terminated' || $session->status === 'completed') {
            Log::info("[ProcessDelayedFlowNode] Session {$this->sessionId} is no longer active. Skipping delayed step.");
            return;
        }

        Log::info("[ProcessDelayedFlowNode] Resuming delayed flow session {$this->sessionId} at node {$this->targetNodeId}");

        $engine->executeNodeSequence($session, $this->targetNodeId);
    }
}
