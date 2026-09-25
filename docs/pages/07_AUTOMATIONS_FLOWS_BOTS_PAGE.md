# 07. Automations, Visual Flows & Bots Specification (`/automations`)

## 1. Overview & Purpose
The Automations page houses the intelligent workflow engines of WhatsCRM: the **Visual Chat Flow Canvas**, **Keyword Chatbots**, **AI Assistant Agents (Gemini/OpenAI)**, and **Dynamic Conversational WA Forms**.

## 2. Layout & Wireframe (Visual Flow Builder Canvas)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Visual Flow: Lead Qualification Bot           [Save Flow]  [Publish 🚀]│
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [Visual Flow Builder]  [Keyword Chatbots]  [AI Agents]  [WA Form Builder]        │
├──────────────┬───────────────────────────────────────────────────────────────────────────────┤
│ NODE PALETTE │ INTERACTIVE REACTFLOW CANVAS                                                  │
│ (Width:220px)│                                                                               │
├──────────────┼───────────────────────────────────────────────────────────────────────────────┤
│ ⚡ Triggers   │  ┌───────────────────────┐                                                     │
│ - Keyword    │  │ ⚡ TRIGGER             │                                                     │
│ - New Chat   │  │ When message contains: │                                                     │
│ - Webhook    │  │ "pricing", "quote"     │                                                     │
│              │  └──────────┬────────────┘                                                     │
│ 💬 Actions   │             ▼                                                                 │
│ - Send Msg   │  ┌───────────────────────┐                                                     │
│ - Send Media │  │ 💬 SEND MESSAGE        │                                                     │
│ - Buttons    │  │ "Hi {{name}}! Which   │                                                     │
│ - List Menu  │  │ plan interests you?"   │                                                     │
│              │  │ [Starter] [Enterprise] │                                                     │
│ 🔀 Logic     │  └──────┬───────────┬────┘                                                     │
│ - Condition  │         │ (Starter) │ (Enterprise)                                            │
│ - Delay      │         ▼           ▼                                                         │
│ - HTTP Webhook ┌───────────────┐ ┌───────────────────────────┐                                │
│ - Assign Agent│ 📄 SEND PDF    │ │ 👥 ASSIGN TO AGENT        │                                │
│ - AI Prompt  │ Starter_Doc.pdf │ │ Agent: Sarah (Sales Head) │                                │
│              │ └───────────────┘ └───────────────────────────┘                                │
└──────────────┴───────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **Visual Flow Builder**: Full-screen canvas powered by ReactFlow with custom drag-and-drop nodes, zooming, panning, mini-map, variable memory inspector, and undo/redo.
2. **Keyword Chatbots**: Simple rule-based table matching exact or partial keywords to instant text/media replies without needing a visual flow.
3. **AI Agents**: Setup AI Assistant personas with custom system prompts, knowledge base context text, temperature, and API keys (Google Gemini 2.5 / OpenAI / DeepSeek).
4. **WA Form Builder**: Drag-and-drop form creator that collects customer input sequentially via WhatsApp (e.g. Step 1: Name, Step 2: Email, Step 3: Service needed) and saves to `wa_form_submissions`.

## 4. Key Node Types in Flow Canvas
- `TriggerNode`: Keyword match, payload trigger, or inbound webhook event.
- `MessageNode`: Rich text with dynamic tags (`{{name}}`, `{{phone}}`).
- `InteractiveButtonNode`: WhatsApp quick reply buttons.
- `InteractiveListNode`: WhatsApp interactive drawer menu.
- `ConditionNode`: If/Else branching based on customer response or business hours.
- `DelayNode`: Paced wait timer (e.g. wait 5 seconds before next reply).
- `HttpRequestNode`: Real-time external API integration (GET/POST with JSON mapping).
- `AssignAgentNode`: Automatically hands over the live chat to a human agent.
- `AiNode`: Queries AI LLM with prompt context and streams reply to user.
