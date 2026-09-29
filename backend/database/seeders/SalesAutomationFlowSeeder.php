<?php

namespace Database\Seeders;

use App\Models\Flow;
use Illuminate\Database\Seeder;

/**
 * SalesAutomationFlowSeeder
 *
 * Creates TWO separate keyword-triggered flows:
 *
 *  Flow 1 — "বিস্তারিত বলুন"
 *    → Sends pricing info + Quick Reply button "হ্যাঁ, ডেমো দেখতে চাই"
 *
 *  Flow 2 — "হ্যাঁ, ডেমো দেখতে চাই"
 *    → Sends the demo website link directly + tags the contact
 */
class SalesAutomationFlowSeeder extends Seeder
{
    public function run(): void
    {
        $workspaceId = 1;

        // ══════════════════════════════════════════════════════════════════
        // FLOW 1 — Trigger: "বিস্তারিত বলুন"
        // ══════════════════════════════════════════════════════════════════
        $flow1Data = [
            'nodes' => [
                [
                    'id'       => 'start',
                    'type'     => 'start',
                    'position' => ['x' => 80, 'y' => 180],
                    'data'     => [
                        'title'     => '⚡ বিস্তারিত বলুন Trigger',
                        'keywords'  => 'বিস্তারিত বলুন',
                        'matchType' => 'contains',
                    ],
                ],
                [
                    'id'       => 'pricing_reply',
                    'type'     => 'send_buttons',
                    'position' => ['x' => 460, 'y' => 160],
                    'data'     => [
                        'title'   => '💰 Pricing Reply',
                        'content' => [
                            'text' => [
                                'body' => "Website + Admin Dashboard সব মিলিয়ে ৭,০০০ টাকা পড়বে। 🎉\n\nDashboard থেকে আপনি নিজেই আপনার পণ্য add, edit, delete করতে পারবেন এবং প্রয়োজন অনুযায়ী website-এর content customize করতে পারবেন।",
                            ],
                        ],
                        'header'  => '',
                        'footer'  => 'আরও জানতে নিচের বোতামে ক্লিক করুন 👇',
                        'buttons' => [
                            ['id' => 'btn_demo', 'title' => 'হ্যাঁ, ডেমো দেখতে চাই'],
                        ],
                    ],
                ],
                [
                    'id'       => 'flow1_end',
                    'type'     => 'end',
                    'position' => ['x' => 860, 'y' => 160],
                    'data'     => [
                        'title'  => '🏁 End',
                        'reason' => 'Pricing sent. Waiting for customer reply.',
                    ],
                ],
            ],
            'edges' => [
                [
                    'id'           => 'e_start_pricing',
                    'source'       => 'start',
                    'target'       => 'pricing_reply',
                    'sourceHandle' => 'default',
                    'animated'     => true,
                    'type'         => 'customWorkflowEdge',
                ],
                [
                    'id'           => 'e_pricing_end',
                    'source'       => 'pricing_reply',
                    'target'       => 'flow1_end',
                    'sourceHandle' => 'default',
                    'animated'     => true,
                    'type'         => 'customWorkflowEdge',
                ],
            ],
        ];

        // ══════════════════════════════════════════════════════════════════
        // FLOW 2 — Trigger: "হ্যাঁ, ডেমো দেখতে চাই"
        // ══════════════════════════════════════════════════════════════════
        $flow2Data = [
            'nodes' => [
                [
                    'id'       => 'start',
                    'type'     => 'start',
                    'position' => ['x' => 80, 'y' => 180],
                    'data'     => [
                        'title'     => '⚡ ডেমো দেখতে চাই Trigger',
                        'keywords'  => 'হ্যাঁ, ডেমো দেখতে চাই, demo, ডেমো',
                        'matchType' => 'contains',
                    ],
                ],
                [
                    'id'       => 'demo_link',
                    'type'     => 'send_message',
                    'position' => ['x' => 460, 'y' => 160],
                    'data'     => [
                        'title'       => '🌐 Demo Website Link',
                        'preview_url' => true,
                        'content'     => [
                            'type' => 'text',
                            'text' => [
                                'preview_url' => true,
                                'body'        => "অবশ্যই ভাই 😊 এইটা আমাদের একটি demo website:\nhttps://marketpro-ecommerce-gamma.vercel.app/\n\nএকটু দেখে জানাবেন কেমন লাগলো। আপনার business অনুযায়ী design ও content customize করে দিতে পারবো। 🚀",
                            ],
                        ],
                    ],
                ],
                [
                    'id'       => 'tag_interested',
                    'type'     => 'set_tag',
                    'position' => ['x' => 840, 'y' => 160],
                    'data'     => [
                        'title'  => '🏷️ Tag: Interested',
                        'label'  => 'Interested',
                        'action' => 'add',
                    ],
                ],
                [
                    'id'       => 'flow2_end',
                    'type'     => 'end',
                    'position' => ['x' => 1200, 'y' => 160],
                    'data'     => [
                        'title'  => '🏁 End',
                        'reason' => 'Demo link sent. Contact tagged as Interested.',
                    ],
                ],
            ],
            'edges' => [
                [
                    'id'           => 'e_start_demo',
                    'source'       => 'start',
                    'target'       => 'demo_link',
                    'sourceHandle' => 'default',
                    'animated'     => true,
                    'type'         => 'customWorkflowEdge',
                ],
                [
                    'id'           => 'e_demo_tag',
                    'source'       => 'demo_link',
                    'target'       => 'tag_interested',
                    'sourceHandle' => 'default',
                    'animated'     => true,
                    'type'         => 'customWorkflowEdge',
                ],
                [
                    'id'           => 'e_tag_end',
                    'source'       => 'tag_interested',
                    'target'       => 'flow2_end',
                    'sourceHandle' => 'default',
                    'animated'     => true,
                    'type'         => 'customWorkflowEdge',
                ],
            ],
        ];

        // ── Upsert Flow 1 ──────────────────────────────────────────────────
        $this->upsertFlow($workspaceId, 'Website Sales – বিস্তারিত বলুন', [
            'description'      => 'Triggered when a customer sends "বিস্তারিত বলুন". Sends pricing info with a Demo button.',
            'trigger_type'     => 'keyword',
            'trigger_keywords' => 'বিস্তারিত বলুন',
            'is_active'        => true,
            'flow_data'        => $flow1Data,
        ]);

        // ── Upsert Flow 2 ──────────────────────────────────────────────────
        $this->upsertFlow($workspaceId, 'Website Sales – হ্যাঁ ডেমো দেখতে চাই', [
            'description'      => 'Triggered when a customer says "হ্যাঁ, ডেমো দেখতে চাই". Sends the demo link and tags the contact.',
            'trigger_type'     => 'keyword',
            'trigger_keywords' => 'হ্যাঁ, ডেমো দেখতে চাই, demo, ডেমো',
            'is_active'        => true,
            'flow_data'        => $flow2Data,
        ]);
    }

    private function upsertFlow(int $workspaceId, string $name, array $attributes): void
    {
        $existing = Flow::where('workspace_id', $workspaceId)
            ->where('name', $name)
            ->first();

        if ($existing) {
            $existing->update($attributes);
            $this->command->info("✅ Updated flow #{$existing->id}: '{$name}'");
        } else {
            $flow = Flow::create(array_merge(['workspace_id' => $workspaceId, 'name' => $name], $attributes));
            $this->command->info("✅ Created flow #{$flow->id}: '{$name}'");
        }
    }
}
