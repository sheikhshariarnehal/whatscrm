# WhatsCRM — Real-time WebSockets & Core Engine Architecture

This document specifies the technical implementations of WhatsCRM's **Real-Time WebSocket Protocol**, **Baileys QR Engine**, **Meta Cloud API Engine**, **Visual Chat Flow Automation Runner**, **Number Warmer Loop**, and **Multi-Channel Adapters**.

---

## 1. Real-Time WebSocket Infrastructure (`socket.js` & `helper/socket/`)

### 1.1 Connection Handshake & Authentication
- **Transport**: WebSockets (`polling` fallback).
- **Authentication**: JWT verification on connection handshake:
  ```js
  const socket = io("http://localhost:3010", {
    query: { token: userOrAgentJwtToken },
    transports: ["websocket", "polling"],
    reconnection: true,
    reconnectionAttempts: 10,
    reconnectionDelay: 2000
  });
  ```
- **Server Handshake Verification**:
  1. Decodes JWT using `process.env.JWTKEY`.
  2. If `role === 'agent'`, queries `agents` table and attaches workspace `owner_uid`.
  3. If normal user, queries `user` table.
  4. Emits `connection_ack` with user metadata and session socket ID.

---

### 1.2 Client-to-Server Event Registry

All client actions are sent using the `message` event with a `{ type, payload }` wrapper:

```javascript
socket.emit("message", {
  type: "<ACTION_TYPE>",
  payload: { /* action payload */ }
});
```

#### Complete List of Client Actions & Payloads:

| Action `type` | Description | Payload Schema | Server Response / Side Effect |
| :--- | :--- | :--- | :--- |
| `get_chat_list` | Fetches filtered, paginated conversation list | `{ search, origin, unreadOnly, assignedOnly, dateRange, limit, offset, filterType, hasNote, statusFilter, agentFilter, labelFilter }` | Emits `chat_list` with array of conversations and meta counts |
| `load_conversation` | Loads full message timeline for a chat | `{ chat_id, limit, offset }` | Emits `load_conversation` with chronological messages |
| `send_chat_message` | Sends a live message across WhatsApp/Telegram/Insta/Messenger | `{ chat_id, message, type: 'text'\|'image'\|'audio'\|'video'\|'document', media_url, caption, origin, sender_mobile }` | Dispatches message via provider adapter, inserts to `beta_conversation`, emits `request_update_opened_chat` |
| `send_new_message` | Initiates a brand new conversation to a new phone number | `{ mobile, name, message, instance_id, origin, country_code }` | Creates record in `beta_chats` & `beta_conversation`, triggers provider |
| `send_new_meta_chat` | Initiates new conversation via WhatsApp Meta Cloud API | `{ phone_number_id, to, template_name, language_code, components }` | Sends template via Meta Graph API, emits `new_meta_chat_created` |
| `send_template_to_conversation` | Dispatches approved Meta template to existing chat | `{ chat_id, template_id, template_name, language, variables }` | Calls Meta Graph API endpoint, records outgoing template message |
| `forward_message` | Forwards selected message to one or multiple contacts | `{ message_id, target_chat_ids: string[] }` | Re-sends message payload to targets, emits `forward_message_done` |
| `set_chat_label` | Adds a color-coded tag/label to a conversation | `{ chat_id, label: { id, title, color } }` | Updates `chat_label` JSON in `beta_chats`, emits `update_labels` |
| `remove_chat_label` | Removes a tag/label from a conversation | `{ chat_id, label_id }` | Updates `chat_label` JSON, emits `update_labels` |
| `add_label` | Creates a new global workspace tag | `{ title, color }` | Inserts into `chat_tags` table, broadcasts `update_labels` |
| `on_label_delete` | Deletes a global workspace tag | `{ id }` | Deletes from `chat_tags` and cleans up conversation associations |
| `save_chat_note` | Adds an internal team note to a conversation | `{ chat_id, note, agent_id, agent_name }` | Inserts note to chat metadata in `beta_chats` |
| `delete_chat_note` | Deletes an internal team note | `{ chat_id, note_id }` | Removes note from chat metadata |
| `assign_agent_to_chat` | Assigns conversation to a specific team agent | `{ chat_id, agent: { id, name, email, uid } }` | Updates `assigned_agent` field, notifies agent socket |
| `unasign_chat_agent` | Unassigns agent from conversation | `{ chat_id }` | Clears `assigned_agent` field |
| `delete_chat` | Deletes conversation and message history | `{ chat_id }` | Removes records from `beta_chats` and `beta_conversation` |
| `del_contact` | Deletes associated phonebook contact | `{ contact_id, mobile }` | Deletes from `contact` / `wa_contacts` tables |
| `save_as_context` | Saves user detail for AI prompt memory | `{ chat_id, context_text }` | Updates AI context memory for automated bot replies |
| `translate_message` | Translates message using OpenAI/Gemini/DeepSeek | `{ message_text, target_language }` | Emits `translation_result` with translated text |
| `suggest_reply` | Generates 3 contextual AI smart reply options | `{ chat_id, last_messages: string[] }` | Emits `suggestion_result` with suggested prompt replies |
| `update_spend_time` | Tracks time agent spent in active chat window | `{ chat_id, seconds_spent }` | Updates agent productivity metrics |
| `export_chats` | Requests CSV/Excel export of filtered chats | `{ filterCriteria }` | Asynchronously builds file and emits `export_chats_result` with download URL |
| `export_conversation`| Requests export of single chat timeline | `{ chat_id, format: 'txt'\|'pdf'\|'json' }` | Emits `export_conversation_result` with download URL |

---

### 1.3 Server-to-Client Event Registry

| Event Name | Trigger Condition | Payload Data Structure |
| :--- | :--- | :--- |
| `connection_ack` | Successful WebSocket auth connection | `{ status: "success", socketId, userData: { uid, name, email, isAgent, owner } }` |
| `chat_list` | Response to `get_chat_list` or conversation update | `{ chats: Array<ChatObject>, total: number, unread_total: number }` |
| `load_conversation` | Response to `load_conversation` | `{ chat_id: string, messages: Array<MessageObject>, has_more: boolean }` |
| `push_new_msg` | Instant incoming or outgoing message | `{ chat_id: string, message: MessageObject, origin: string }` |
| `update_conversations` | New incoming message requiring chat list reorder | `{ chat_id: string, last_message: string, unread_count: number, updatedAt: string }` |
| `request_update_chat_list` | Signal from server to re-fetch chat list | `{ reason: string, updated_chat_id?: string }` |
| `request_update_opened_chat` | Signal to re-fetch currently active chat | `{ chat_id: string }` |
| `update_labels` | Tag created, modified, or assigned | `{ labels: Array<LabelObject> }` |
| `translation_result` | AI translation response | `{ original: string, translated: string, target_language: string }` |
| `suggestion_result` | AI smart reply suggestions | `{ suggestions: string[] }` |
| `forward_message_done` | Forwarding operation completed | `{ success: boolean, count: number }` |
| `error` | Socket operation exception | `{ message: string, code?: string }` |

---

## 2. WhatsApp Baileys QR Engine (`helper/addon/qr/`)

WhatsCRM embeds `@whiskeysockets/baileys` (v7.0) to provide full WhatsApp Web emulation without requiring official Meta Business verification.

### 2.1 Multi-Session Architecture
- **Session Keys**: Stored per instance (`instance_id` / `session_id`).
- **Storage Backends**:
  1. **MySQL (`mysql-baileys`)**: Stores session creds and keys in the `auth` table (recommended for clustered/cloud deployment).
  2. **MongoDB**: Stores session state in `wacrm_session` MongoDB collections.
  3. **Local Filesystem**: Multi-file auth state in `sessions/md_<instance_id>/`.
- **Configuration Toggle**: Configured dynamically from the `web_private` table (`qr_storage` column).

### 2.2 QR Code Pairing Lifecycle
1. User clicks **"Pair New WhatsApp Number"** on frontend.
2. Frontend calls `POST /api/qr/create_session` with `{ session_name }`.
3. Server generates session ID, creates Baileys socket with `useMultiFileAuthState` / `mysql-baileys`.
4. Baileys emits `connection.update` containing raw QR string.
5. Server converts QR string to base64 DataURL via `qrcode.toDataURL()` and streams to frontend.
6. User scans QR code with WhatsApp mobile app.
7. Baileys receives `connection.update: { connection: 'open' }`, extracts connected phone number, updates `instance` table (`status = 'connected'`, `phone = '...'`).
8. Real-time hook pushes connected event to frontend UI.

---

## 3. Official WhatsApp Meta Cloud API Engine

WhatsCRM integrates natively with Meta Graph API v18+ for high-volume enterprise messaging.

### 3.1 Credentials Storage (`meta_api` Table)
- `app_id`: Meta Developer App ID.
- `phone_number_id`: WhatsApp Business Phone Number ID.
- `waba_id`: WhatsApp Business Account ID.
- `token`: Permanent System User Access Token.
- `webhook_token`: Inbound webhook verification token.

### 3.2 Inbound Webhook Processing (`routes/webhook.js`)
- **GET Verification**: Responds to Meta webhook challenge (`hub.challenge`).
- **POST Inbound Dispatcher**:
  - Unpacks `entry[].changes[].value.messages[]`.
  - Downloads incoming audio/image/video/document via Meta Media Graph API (`https://graph.facebook.com/v18.0/{media_id}`) and saves locally to `client/public/meta-media/`.
  - Inserts message into `beta_chats` & `beta_conversation`.
  - Evaluates keyword chatbot rules (`chatbot` table) and visual flow triggers (`flow` table).
  - Emits real-time `push_new_msg` and `update_conversations` to workspace WebSocket room.

---

## 4. Visual Chat Flow Automation Engine (`automation/`)

The flow automation engine processes visual node graphs created on the visual canvas.

### 4.1 Node Types & Structure
- `trigger`: Keyword match, exact match, regex, webhook trigger, or new chat initiation.
- `message`: Text message with template variable interpolation (`{{name}}`, `{{phone}}`, `{{custom_field}}`).
- `media`: Send image, audio voice note, video, or PDF document.
- `interactive_button`: Interactive CTA buttons or quick-reply options.
- `interactive_list`: Multi-option selectable menu drawer.
- `condition`: Logical branching (e.g., `IF variable == 'VIP' THEN Node_A ELSE Node_B`).
- `delay`: Paced delay node (`sleep_seconds`).
- `http_request`: External API webhook (URL, Method, Headers, JSON Body, Response mapping).
- `assign_agent`: Assigns conversation to designated team member.
- `ai_response`: Dynamically invokes Google Gemini or OpenAI with chat context.

### 4.2 Execution Pipeline (`automation/functions.js`)
```
Incoming Message 
  └──> Check Active Flow Session (in `flow_session` table)
        ├── If Exists: Continue execution from current `node_id`
        └── If None: Scan `flow` table for matching trigger
              └── Instantiate new session in `flow_session`
                    └── Traverse Graph Edges (Breadth-First / Sequential)
                          ├── Execute Node Action
                          ├── Update Flow Session Variables
                          └── Wait for Next User Input (if interactive node)
```

---

## 5. Number Warmer Engine (`helper/addon/qr/warmer/`)

The WhatsApp Number Warmer is an automated safety subsystem designed to warm up newly paired WhatsApp numbers and prevent spam bans.

### 5.1 Peer-to-Peer Warm Matrix
- Connects 2 or more paired numbers in a private peer loop.
- Reads conversational dialog scripts from `warmer_script` table.
- Simulates realistic human timing:
  - Random typing indicators (`sendPresenceUpdate('composing')`).
  - Randomized reading delays (5–20 seconds).
  - Paced daily volume progression (e.g., Day 1: 20 msgs, Day 2: 40 msgs, Day 7: 150 msgs).
- Keeps track of warmup statistics in `warmers` table.

---

## 6. High-Throughput Campaign Loops (`loops/`)

### 6.1 Meta Campaign Loop (`loops/campaignBeta.js`)
- Runs a non-blocking asynchronous interval loop.
- Queries `beta_campaign` table where `status = 'pending'`.
- Chunks contact recipient arrays into concurrent batches.
- Replaces template placeholders with row-level contact data.
- Records real-time delivery status (`sent`, `failed`, `delivered`) into `beta_campaign_logs`.

### 6.2 QR Broadcast Campaign Loop (`loops/qrCampaignLoop.js`)
- Queries `qr_campaigns` table for scheduled and pending jobs.
- Rotates across multiple active paired WhatsApp Web sessions (multi-device round-robin).
- Enforces user-configured throttling delays (e.g., 8–15 seconds per message) to protect accounts from carrier anti-spam triggers.
- Logs message delivery receipts in `qr_campaign_logs`.
