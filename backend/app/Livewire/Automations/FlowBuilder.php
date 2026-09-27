<?php

namespace App\Livewire\Automations;

use App\Models\Flow;
use Livewire\Component;

class FlowBuilder extends Component
{
    public int $flowId;
    public string $flowName = '';
    public string $flowDescription = '';
    public string $flowTriggerKeywords = '';
    public bool $isActive = true;
    public array $graphData = [];
    public bool $saveSuccess = false;

    public function mount(int $id)
    {
        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);
        $flow = Flow::where('workspace_id', $workspaceId)->findOrFail($id);

        $this->flowId = $flow->id;
        $this->flowName = $flow->name;
        $this->flowDescription = $flow->description ?? '';
        $this->flowTriggerKeywords = $flow->trigger_keywords ?? '';
        $this->isActive = (bool) $flow->is_active;

        $data = $flow->flow_data ?? [];
        if (empty($data['nodes'])) {
            $data = [
                'nodes' => [
                    [
                        'id' => 'initialNode',
                        'type' => 'INITIAL',
                        'position' => ['x' => 100, 'y' => 260],
                        'data' => [
                            'title' => '⚡ Inbound Trigger',
                            'keywords' => $flow->trigger_keywords,
                        ],
                    ],
                    [
                        'id' => 'msg_' . time(),
                        'type' => 'SEND_MESSAGE',
                        'position' => ['x' => 480, 'y' => 240],
                        'data' => [
                            'title' => '💬 Welcome Message',
                            'content' => [
                                'type' => 'text',
                                'text' => [
                                    'preview_url' => true,
                                    'body' => "Hello {{{name}}}! Welcome to our WhatsApp service. How can we help you today?",
                                ],
                            ],
                        ],
                    ],
                ],
                'edges' => [
                    [
                        'id' => 'edge_initial_msg',
                        'source' => 'initialNode',
                        'target' => 'msg_' . time(),
                    ],
                ],
            ];
        }

        $this->graphData = $data;
    }

    public function saveGraph(string $jsonPayload)
    {
        $decoded = json_decode($jsonPayload, true);
        if (!$decoded || !isset($decoded['nodes'])) {
            session()->flash('error', 'Invalid graph structure.');
            return;
        }

        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);
        $flow = Flow::where('workspace_id', $workspaceId)->findOrFail($this->flowId);

        $flow->update([
            'name' => $this->flowName,
            'description' => $this->flowDescription,
            'trigger_keywords' => $this->flowTriggerKeywords,
            'is_active' => $this->isActive,
            'flow_data' => $decoded,
        ]);

        $this->graphData = $decoded;
        $this->saveSuccess = true;
        session()->flash('success', 'Visual chat flow published successfully!');
    }

    public function toggleActive()
    {
        $this->isActive = !$this->isActive;
        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);
        Flow::where('workspace_id', $workspaceId)->where('id', $this->flowId)->update([
            'is_active' => $this->isActive,
        ]);
    }

    public function render()
    {
        return view('livewire.automations.flow-builder', [
            'flowId' => $this->flowId,
            'flowName' => $this->flowName,
            'flowDescription' => $this->flowDescription,
            'flowTriggerKeywords' => $this->flowTriggerKeywords,
            'isActive' => $this->isActive,
            'graphData' => $this->graphData,
        ])->layout('layouts.app');
    }
}
