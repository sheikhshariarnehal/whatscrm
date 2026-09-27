<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlowBuilderApiController extends Controller
{
    /**
     * Get flow data and configuration for the visual builder.
     */
    public function show(int $id): JsonResponse
    {
        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);
        $flow = Flow::where('workspace_id', $workspaceId)->findOrFail($id);

        $flowData = $flow->flow_data ?? [];
        if (empty($flowData['nodes'])) {
            $flowData = [
                'nodes' => [
                    [
                        'id' => 'initialNode',
                        'type' => 'start',
                        'position' => ['x' => 100, 'y' => 260],
                        'data' => [
                            'title' => '⚡ Inbound Trigger',
                            'keywords' => $flow->trigger_keywords ?: 'Any inbound message',
                        ],
                    ],
                    [
                        'id' => 'msg_' . time(),
                        'type' => 'send_message',
                        'position' => ['x' => 480, 'y' => 240],
                        'data' => [
                            'title' => '💬 Welcome Message',
                            'content' => [
                                'type' => 'text',
                                'text' => [
                                    'preview_url' => true,
                                    'body' => "Hello {{contact.name}}! Welcome to our WhatsApp service. How can we help you today?",
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
                        'sourceHandle' => 'default',
                    ],
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'flow' => [
                'id' => $flow->id,
                'name' => $flow->name,
                'description' => $flow->description ?? '',
                'trigger_keywords' => $flow->trigger_keywords ?? '',
                'trigger_type' => $flow->trigger_type ?? 'keyword',
                'is_active' => (bool) $flow->is_active,
                'flow_data' => $flowData,
                'created_at' => $flow->created_at?->toISOString(),
                'updated_at' => $flow->updated_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Save the visual flow graph and metadata.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);
        $flow = Flow::where('workspace_id', $workspaceId)->findOrFail($id);

        $payload = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'trigger_keywords' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'flow_data' => 'required',
        ]);

        $graphData = $payload['flow_data'];
        if (is_string($graphData)) {
            $graphData = json_decode($graphData, true);
        }

        if (!is_array($graphData) || !isset($graphData['nodes'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid graph data structure. Must contain a "nodes" array.',
            ], 422);
        }

        $updateData = [
            'flow_data' => $graphData,
        ];

        if (isset($payload['name'])) {
            $updateData['name'] = $payload['name'];
        }
        if (isset($payload['description'])) {
            $updateData['description'] = $payload['description'];
        }
        if (isset($payload['trigger_keywords'])) {
            $updateData['trigger_keywords'] = $payload['trigger_keywords'];
        }
        if (isset($payload['is_active'])) {
            $updateData['is_active'] = (bool) $payload['is_active'];
        }

        $flow->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Visual WhatsApp flow published successfully!',
            'flow' => [
                'id' => $flow->id,
                'name' => $flow->name,
                'description' => $flow->description ?? '',
                'trigger_keywords' => $flow->trigger_keywords ?? '',
                'is_active' => (bool) $flow->is_active,
                'flow_data' => $flow->flow_data,
                'updated_at' => $flow->updated_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Toggle flow active/paused state.
     */
    public function toggleActive(int $id): JsonResponse
    {
        $workspaceId = session('current_workspace_id', auth()->user()->currentWorkspace()->id ?? 1);
        $flow = Flow::where('workspace_id', $workspaceId)->findOrFail($id);

        $flow->is_active = !$flow->is_active;
        $flow->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $flow->is_active,
            'message' => $flow->is_active ? 'Flow activated' : 'Flow paused',
        ]);
    }
}
