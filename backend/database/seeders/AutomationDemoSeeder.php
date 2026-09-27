<?php

namespace Database\Seeders;

use App\Models\BotBinding;
use App\Models\Flow;
use App\Models\WaForm;
use Illuminate\Database\Seeder;

class AutomationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $flow = Flow::first();
        if ($flow) {
            $flow->flow_data = [
                'nodes' => [
                    [
                        'id' => 'initialNode',
                        'type' => 'INITIAL',
                        'position' => ['x' => 80, 'y' => 240],
                        'data' => [
                            'title' => 'Inbound Trigger',
                            'keywords' => 'demo, pricing, quote, consult',
                        ]
                    ],
                    [
                        'id' => 'msg_welcome',
                        'type' => 'SEND_MESSAGE',
                        'position' => ['x' => 450, 'y' => 200],
                        'data' => [
                            'title' => 'Welcome Greeting',
                            'content' => [
                                'type' => 'text',
                                'text' => [
                                    'preview_url' => true,
                                    'body' => 'Hello {{{name}}}! Welcome to our automated service. Are you looking for Sales or Support?'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 'cond_routing',
                        'type' => 'CONDITION',
                        'position' => ['x' => 820, 'y' => 200],
                        'data' => [
                            'title' => 'Branch on Query',
                            'conditions' => [
                                ['type' => 'text_contains', 'value' => 'sales', 'targetNodeId' => 'msg_sales'],
                                ['type' => 'text_contains', 'value' => 'support', 'targetNodeId' => 'ai_support'],
                            ]
                        ]
                    ],
                    [
                        'id' => 'msg_sales',
                        'type' => 'SEND_MESSAGE',
                        'position' => ['x' => 1200, 'y' => 100],
                        'data' => [
                            'title' => 'Sales Routing',
                            'content' => [
                                'type' => 'text',
                                'text' => [
                                    'preview_url' => true,
                                    'body' => 'Awesome! Our starter package starts at $29/mo. An available sales agent will be with you shortly.'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 'ai_support',
                        'type' => 'AI_TRANSFER',
                        'position' => ['x' => 1200, 'y' => 340],
                        'data' => [
                            'title' => 'AI Support Agent',
                            'systemPrompt' => 'You are an intelligent technical support agent. Help the user diagnose their issue.'
                        ]
                    ]
                ],
                'edges' => [
                    ['id' => 'e1', 'source' => 'initialNode', 'target' => 'msg_welcome'],
                    ['id' => 'e2', 'source' => 'msg_welcome', 'target' => 'cond_routing'],
                    ['id' => 'e3', 'source' => 'cond_routing', 'target' => 'msg_sales'],
                    ['id' => 'e4', 'source' => 'cond_routing', 'target' => 'ai_support'],
                ]
            ];
            $flow->save();

            BotBinding::firstOrCreate([
                'workspace_id' => 1,
                'title' => 'Official Meta Cloud API Bot',
                'channel' => 'meta',
                'origin_id' => 'META_CLOUD_API',
                'flow_id' => $flow->id,
                'is_active' => true,
            ]);

            BotBinding::firstOrCreate([
                'workspace_id' => 1,
                'title' => 'Main Baileys QR Session Bot',
                'channel' => 'qr',
                'origin_id' => 'ALL_DEVICES',
                'flow_id' => $flow->id,
                'is_active' => true,
            ]);

            WaForm::firstOrCreate([
                'workspace_id' => 1,
                'name' => 'Customer Lead & Intake Form',
            ], [
                'description' => 'Collect customer contact details and interested CRM services directly inside WhatsApp.',
                'meta_flow_id' => 'FLOW_958428203645439',
                'flow_status' => 'PUBLISHED',
                'categories' => ['CUSTOMER_SUPPORT', 'LEAD_GENERATION'],
                'fields_schema' => [
                    ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'TextInput', 'required' => true, 'placeholder' => 'Hamid Saifi'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'TextInput', 'required' => true, 'placeholder' => 'hamid@example.com'],
                    ['name' => 'service', 'label' => 'Select Service', 'type' => 'Dropdown', 'required' => true, 'options' => ['WhatsApp CRM', 'Meta Cloud API Setup', 'Custom Chatbot Flow']],
                    ['name' => 'timeline', 'label' => 'Preferred Launch Date', 'type' => 'DatePicker', 'required' => false],
                ],
            ]);
        }
    }
}
