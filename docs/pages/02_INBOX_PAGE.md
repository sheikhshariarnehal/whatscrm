# 02. Omnichannel Multi-Channel Live Inbox Specification (`/inbox`)

## 1. Overview & Purpose
The Inbox is the central nervous system of WhatsCRM. It unifies messages from **WhatsApp Cloud API**, **WhatsApp Baileys QR Web**, **Telegram**, **Instagram DM**, and **Facebook Messenger** into a synchronized 3-panel workspace with sub-second real-time latency.

## 2. Layout & Wireframe (3-Panel Architecture)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Unified Live Inbox                               [Search ⌘K]  [Agent Status: 🟢] │
├──────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [All (42)]  [WhatsApp (28)]  [Instagram (6)]  [Telegram (5)]  [Messenger (3)]  [Unassigned]  │
├──────────────────────────┬───────────────────────────────────────────┬───────────────────────────────────┤
│ PANEL 1: CHAT LIST       │ PANEL 2: ACTIVE CONVERSATION CANVAS       │ PANEL 3: CONTACT & CRM SIDEBAR    │
│ (Width: 320px)           │ (Width: Flex-1)                           │ (Width: 300px)                    │
├──────────────────────────┼───────────────────────────────────────────┼───────────────────────────────────┤
│ [🔍 Search chats or tags]│ 👤 Jane Doe (+1 555-0199) [WhatsApp 🟢]   │ 👤 Contact Details                │
│ 🏷️ Filters: [All ▾] [Tag▾]│ Assigned: Sarah M. | Kanban: [Negotiation]│ Jane Doe (+1 555-0199)            │
│                          │ ───────────────────────────────────────── │ Email: jane@example.com           │
│ 🟢 Jane Doe (WhatsApp)   │ [10:24 AM] Jane:                          │ Location: New York, USA           │
│   "Need pricing quote"   │   Hi there! I would like to get a quote.  │ ───────────────────────────────── │
│   10:24 AM • [🏷️ Lead]   │                                           │ 🏷️ Labels & Tags                 │
│                          │ [10:25 AM] Agent Sarah:                   │ [+ Add Tag] [🔥 High Value] [VIP] │
│ 🟣 Acme Corp (Instagram) │   Sure Jane! Here is our product catalog. │ ───────────────────────────────── │
│   "Can we partner?"      │   📄 Catalog_2026.pdf                     │ 📌 Internal Notes                 │
│   09:50 AM • [🏷️ Collab] │                                           │ - Prefers delivery by Friday.     │
│                          │ [10:26 AM] Jane (Voice Note):             │ [+ Add Internal Note]             │
│ 🔵 Alex (Telegram)       │   ▶ ılılılılılılılıl 0:14                 │ ───────────────────────────────── │
│   "Order status?"        │                                           │ 🎯 CRM Kanban Stage               │
│   Yesterday • [🏷️ Support]│ ───────────────────────────────────────── │ Stage: [Negotiation & Proposal ▾] │
│                          │ [AI Smart Reply: "I can assist..." ⚡]    │ Deal Value: $5,000                │
│                          │ ───────────────────────────────────────── │ ───────────────────────────────── │
│                          │ [📎 Media] [🎤 Voice] [📄 Template]       │ 👥 Assigned Agent                 │
│                          │ [ Type your message...            ] [Send]│ [ Sarah Miller ▾ ]                │
└──────────────────────────┴───────────────────────────────────────────┴───────────────────────────────────┘
```

## 3. Sub-Tabs & Filter Views
- **All Chats**: Unified stream of all 5 channels.
- **WhatsApp**: Filtered to Meta Cloud API and Baileys QR sessions.
- **Instagram**: Filtered to connected Instagram Professional accounts.
- **Telegram**: Filtered to Telegram Bot and User client sessions.
- **Messenger**: Filtered to Facebook Page direct messages.
- **Unassigned / Mine**: Filtered by agent assignment.
- **Custom Tag Filter**: Filtered by color tags.

## 4. Rich Chat Features
1. **Interactive Audio Voice Notes**: Integrated WaveSurfer audio visualizer with play/pause, scrub, and playback speed (1x, 1.5x, 2x).
2. **AI Smart Reply**: 1-click generation of 3 context-aware response suggestions powered by Google Gemini / OpenAI.
3. **Live Message Translation**: Instant on-the-fly translation of incoming foreign messages.
4. **Meta Template Selector**: Search and dispatch pre-approved Meta WhatsApp templates with custom variable placeholders.
5. **Multi-Media Attachment Support**: Images, Videos, Audio, PDFs, Office docs, Contact vCards, Location pins.
6. **Internal Collaboration Notes**: Private agent notes displayed only to team members, not sent to the customer.

## 5. WebSocket Events (Full Real-Time Matrix)
- Client Emits: `get_chat_list`, `load_conversation`, `send_chat_message`, `send_template_to_conversation`, `set_chat_label`, `assign_agent_to_chat`, `save_chat_note`, `translate_message`, `suggest_reply`.
- Server Listens & Pushes: `chat_list`, `load_conversation`, `push_new_msg`, `update_conversations`, `request_update_opened_chat`, `translation_result`, `suggestion_result`.
