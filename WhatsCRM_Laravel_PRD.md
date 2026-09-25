# Product Requirements Document
## WhatsCRM — Omnichannel WhatsApp CRM Platform (Laravel + MySQL Rebuild)

**Version:** 1.0
**Date:** September 26, 2026
**Status:** Draft for engineering review
**Source material analyzed:** `PRODUCT.md`, `ARCHITECTURE.md`, `ALL_PAGES.md`, `API_SPECIFICATION.md`, `DATABASE_SCHEMA.md`, `REALTIME_AND_ENGINES.md`, `import.sql`

---

## 1. Executive Summary

WhatsCRM is an enterprise omnichannel customer-engagement and CRM platform unifying **WhatsApp (Meta Cloud API + unofficial Baileys QR sessions)**, **Telegram**, **Instagram DM**, and **Facebook Messenger** into a single inbox, paired with a visual automation builder, Kanban sales pipeline, mass-campaign engine, AI assistants, and a full multi-tenant SaaS back office.

The reference implementation is a **Node.js/Express + Socket.IO** backend with a **React 19 SPA** frontend and a **62-table MySQL/MariaDB** schema (378 REST endpoints across 23 route modules, 12 top-level application pages).

This PRD defines the requirements for rebuilding the same product on **Laravel (PHP) + MySQL**, preserving full feature parity while adapting the architecture to idiomatic Laravel patterns (Eloquent, Queues, Broadcasting, Sanctum, Horizon). It also flags the places where a straight "port" is not possible — mainly the Node-only WhatsApp Web (Baileys) and Telegram (MTProto) protocol libraries — and proposes a hybrid architecture to resolve them.

---

## 2. Goals & Non-Goals

### 2.1 Goals
1. Re-platform the backend from Node/Express to **Laravel 11/12**, and the primary datastore to **MySQL 8**, with **zero feature regression** against the 23 existing route modules.
2. Preserve real-time behavior (live inbox, QR pairing, campaign progress, agent presence) using Laravel's native broadcasting stack instead of a bespoke Socket.IO layer.
3. Preserve multi-tenancy, RBAC (SuperAdmin → Workspace Owner → Team Agent), and plan/quota enforcement.
4. Keep the existing React 19 + Vite frontend as the primary client where practical, connecting to Laravel via REST + WebSockets, to protect the UI investment described in `PRODUCT.md`/`ALL_PAGES.md`.
5. Produce a schema that is portable from the existing `import.sql` dump via Laravel migrations, with referential integrity and proper foreign keys added where the legacy schema lacked them.
6. Ship in phases so the highest-value modules (Inbox, Devices, CRM, Contacts, Campaigns) are usable before the long tail (Landing CMS, Voice Calls, Number Warmer) is complete.

### 2.2 Non-Goals (v1)
- Rebuilding the public marketing site / theme CMS pixel-for-pixel (module 19–20, `/api/theme`, `/api/web`) — ship a minimal static landing page first, defer the full drag-and-drop CMS.
- WhatsApp **Voice & Calls** (`/api/wa_call`, 19 endpoints) — WebRTC voice bots are high-complexity and low-usage; defer to Phase 5.
- Telegram **MTProto personal-number login** (as opposed to Bot API) — defer per the architectural note in §6.4.
- Guaranteeing byte-for-byte API compatibility with the old endpoint paths — we will version the new API (`/api/v1/...`) and keep the same *capabilities*, not identical routes.

---

## 3. Users & Roles

| Role | Description | Key Capabilities |
|---|---|---|
| **Super Administrator** | Anthropic-style platform owner, one per install | Manage SaaS plans/pricing, payment gateways (Stripe, MercadoPago), SMTP, translations, landing CMS, global analytics, tenant suspension |
| **Workspace Owner** | Tenant admin, one per company account, quota-bound by plan | Connect channels (WA Cloud API, Baileys QR, Telegram, Instagram, Messenger), manage contacts/CRM/campaigns/automations, invite & manage agents, billing |
| **Team Agent** | Seat under a workspace, permission-scoped | View/reply to assigned chats, manage assigned Kanban deals, use quick replies & notes, limited by RBAC flags (e.g., "can send," "can see unmasked numbers") |

This mirrors the RBAC hierarchy in `ARCHITECTURE.md §2.6` and should be modeled as: `admins` (platform), `users` (workspace owners, tenant-scoped), `agents` (belongs to a `user_id` owner) — implemented with **Laravel Sanctum** for token auth and **Spatie `laravel-permission`** for role/permission enforcement instead of hand-rolled JWT middleware.

---

## 4. Product Scope — Page-by-Page (12 Core Pages)

| # | Page | Route | Priority | Summary |
|---|---|---|---|---|
| 1 | Dashboard | `/dashboard` | P0 | KPI command center: message volume trend, channel split, live incoming audit feed |
| 2 | Unified Live Inbox | `/inbox` | P0 | 3-panel omnichannel chat (WhatsApp/Telegram/Instagram/Messenger), voice notes, AI smart replies, CRM sidebar |
| 3 | WhatsApp & Devices | `/devices` | P0 | Baileys QR pairing, Meta Cloud API credentials, Number Warmer matrix |
| 4 | CRM Pipeline | `/crm` | P0 | Drag-and-drop Kanban, deal values/stages, 1-click WhatsApp launch |
| 5 | Contacts & Phonebook | `/contacts` | P0 | Contact DB, groups, custom fields, 3-step CSV import wizard |
| 6 | Campaigns & Broadcasts | `/campaigns` | P1 | Mass outreach (Cloud API & QR), anti-ban sleep timers, template variable mapping, live logs |
| 7 | Automations & Bots | `/automations` | P1 | Visual node-graph flow builder, keyword auto-replies, AI persona assistants, WA form builder |
| 8 | Team & Agents | `/team` | P1 | Multi-seat management, presence, internal task board, RBAC |
| 9 | Developer & Webhooks | `/developer` | P2 | Inbound webhooks (WooCommerce/Shopify), API keys, payload debugger, API console |
| 10 | SuperAdmin Back-Office | `/admin` | P2 | Tenant management, pricing packages, payment gateways, translations, CMS |
| 11 | Workspace Settings | `/settings` | P1 | Workspace profile, logo, sound alerts, FCM push, active plan/billing |
| 12 | Public Portal & Auth | `/`, `/login` | P2 | Landing page, pricing grid, Google OAuth login, tenant registration, password recovery |

**Suggested phase grouping:** P0 pages = Phase 1–2 (MVP), P1 = Phase 3, P2 = Phase 4–5. See §11 Roadmap.

---

## 5. Functional Requirements by Domain

### 5.1 Multi-Channel Unified Inbox
- Aggregate conversations from WhatsApp (both connection types), Telegram, Instagram, and Messenger into one paginated, filterable list (search, origin, unread-only, assigned-only, date range, tag, agent, status).
- Real-time message delivery status (sent/delivered/read/failed) surfaced per message.
- Send text, image, audio (voice note), video, and document messages; forward a message to one or many chats.
- Per-conversation: colored labels/tags (workspace-global tag catalog), internal notes, agent assignment/unassignment, AI context memory for bot replies.
- AI-assisted features: one-click translation of any message, 3-option smart-reply suggestions, generated from chat history via the configured LLM provider (Gemini / OpenAI / DeepSeek).
- Async export: full chat list (CSV/Excel) and single-conversation transcript (TXT/PDF/JSON).
- Time-on-chat tracking per agent for productivity metrics.

### 5.2 Channel Connections
- **WhatsApp Meta Cloud API**: store `app_id`, `phone_number_id`, `waba_id`, permanent token, webhook verify token; sync/approve message templates; verified webhook GET challenge + POST ingestion; inbound media download via Graph Media API.
- **WhatsApp Web (Baileys/QR)**: multi-session pairing via QR code, session status (connecting/open/closed), auto-reconnect, session storage abstraction (MySQL / local disk — see §6.4 for architecture note; MongoDB backend dropped in v1 in favor of a single supported backend).
- **Telegram**: Bot API channel messaging (v1); MTProto personal-number login deferred (§6.4/§9).
- **Instagram DM** and **Facebook Messenger**: Meta Graph API webhook-based messaging, page/account linkage, token storage.
- **Number Warmer**: pair 2+ connected WhatsApp numbers, run scripted peer-to-peer conversations with randomized typing/read delays and a ramped daily volume schedule, to reduce ban risk on freshly paired numbers.

### 5.3 CRM Pipeline (Kanban)
- Configurable pipeline stages, drag-and-drop deal cards, deal value, linked contact, one-click "open WhatsApp chat" from a card.
- Stage-change triggers usable as automation conditions (see §5.5).

### 5.4 Contacts & Phonebook
- Central contact database with custom field definitions per workspace.
- Group manager (static/dynamic groups) used as recipient lists for campaigns.
- 3-step guided CSV bulk import (upload → column mapping → validation/dedupe preview → commit) with error reporting.
- Contact export.

### 5.5 Automations & Visual Flow Builder
- Node-graph canvas (drag/connect) supporting node types: `trigger` (keyword/exact/regex/webhook/new-chat), `message`, `media`, `interactive_button`, `interactive_list`, `condition` (variable/regex/business-hours branching), `delay`, `http_request` (external webhook call with response mapping), `assign_agent`, `ai_response` (LLM-generated reply with context), and CRM actions (Kanban stage change, tag add/remove).
- Stateful execution: an incoming message resumes an existing `flow_session` at its current node, or triggers a new session by matching against active flow trigger rules; session carries a variable-memory bag.
- Simple keyword chatbot rules (exact/partial match) as a lightweight alternative to the full flow builder.
- AI persona assistants configurable per workspace (system prompt, model, temperature) usable as an `ai_response` node or as the default fallback responder.
- Conversational lead-capture **form builder** (dynamic questions, validation rules, submission storage) deliverable inside a chat flow.

### 5.6 Campaigns & Broadcasts
- Mass-send wizard for both Meta Cloud API (approved templates + variable mapping) and QR sessions (raw messages), targeting a phonebook group or CSV upload.
- Anti-ban controls: configurable random sleep interval between sends, multi-device round-robin distribution across paired QR sessions for QR campaigns.
- Async background dispatch with per-recipient delivery logging (`sent`/`failed`/`delivered`) and a live progress view.
- CSV/Excel export of campaign logs.

### 5.7 Team & Agents
- Invite/manage agent seats (subject to plan seat limit), toggle active/inactive, presence indicator.
- Per-agent RBAC flags (e.g., allowed to send messages, allowed to see full/masked phone numbers).
- Internal follow-up task board assignable to agents, with completion tracking.
- Agent-scoped chat assignment view (`my assigned chats`).

### 5.8 Developer Tools
- Per-workspace API key issuance for the external Developer REST API (send message, send template, campaign trigger, log retrieval/deletion).
- Inbound webhook listeners with payload-field mapping for common e-commerce platforms (WooCommerce, Shopify) and custom sources.
- Interactive API console and webhook payload debugger/log viewer.

### 5.9 SuperAdmin Back-Office
- Tenant (workspace) directory: view/suspend/delete, impersonate ("auto-login") for support.
- Subscription plan CRUD (seat limits, channel limits, message quotas, feature flags), order/invoice history.
- Payment gateway configuration (Stripe, MercadoPago, others), SMTP configuration + test send.
- Translation editor for i18n strings; landing-page CMS (pages, FAQ, testimonials, partner logos, blog).
- Platform-wide analytics dashboard.

### 5.10 Workspace Settings
- Workspace profile (name, logo, business info), notification sound preferences, FCM web-push registration, current plan/usage summary, billing history/upgrade CTA.

### 5.11 Public Portal & Auth
- Marketing landing page driven by CMS content, dynamic pricing grid pulled from live plans.
- Tenant self-registration, email/password login, Google OAuth, password recovery flow.

---

## 6. System Architecture

### 6.1 Target Topology

```mermaid
flowchart TB
    subgraph Client["Client — React 19 + Vite SPA (retained)"]
        UI[UI Components]
        Echo[Laravel Echo\n(WebSocket client)]
    end

    subgraph Laravel["Laravel Application (Primary Backend)"]
        API[REST API\n(routes/api.php, versioned /api/v1)]
        Sanctum[Sanctum Auth\n+ Spatie Permissions]
        Reverb[Laravel Reverb\n(WebSocket broadcasting server)]
        Queues[Queue Workers\n(Horizon)]
        Scheduler[Task Scheduler\n(cron-driven)]
        Jobs[Jobs: Campaign Dispatch,\nWarmer Ticks, Flow Execution,\nExports, Webhook Fan-out]
    end

    subgraph Bridge["WA/Telegram Bridge Service (Node.js microservice)"]
        Baileys[Baileys Multi-Session Engine]
        MTProto[Telegram MTProto Client\n(optional, Phase 5+)]
    end

    subgraph External["External APIs"]
        MetaCloud[Meta Graph API\n(WA Cloud, Instagram, Messenger)]
        LLMs[Gemini / OpenAI / DeepSeek]
        Payments[Stripe / MercadoPago]
    end

    subgraph Data["Data Layer"]
        MySQL[(MySQL 8 — Eloquent Models)]
        Redis[(Redis — Queues, Cache,\nBroadcasting, Reverb pub/sub)]
        Storage[(S3 / Laravel Filesystem\n— media, exports)]
    end

    UI <--> API
    Echo <--> Reverb
    API --> Sanctum
    API <--> MySQL
    API --> Jobs
    Jobs --> Queues --> Redis
    Reverb <--> Redis
    Jobs --> MetaCloud
    Jobs --> LLMs
    Jobs --> Payments
    API <-->|Internal REST + Signed Webhooks| Bridge
    Bridge <-->|session state| MySQL
    Bridge --> Storage
    Scheduler --> Jobs
```

### 6.2 Backend Framework Mapping

| Legacy (Node/Express) | Laravel Equivalent |
|---|---|
| Express routes (`routes/*.js`, 23 modules, 378 endpoints) | Laravel API resource controllers under `app/Http/Controllers/Api/`, grouped by domain, versioned at `/api/v1` |
| Custom JWT middleware | **Laravel Sanctum** (SPA token/cookie auth) + **Spatie laravel-permission** for role/permission middleware |
| Socket.IO server (`socket.js`) | **Laravel Reverb** (first-party, Pusher-protocol-compatible WebSocket server) + **Laravel Echo** on the frontend; broadcast on private/presence channels scoped per workspace (`private-workspace.{id}`, `presence-workspace.{id}.agents`) |
| `loops/` (setInterval workers: campaign loop, QR campaign loop, warmer loop) | **Laravel Queues** (Redis driver) + **Queued Jobs** for per-recipient dispatch, with **Laravel Scheduler** ticking the loop cadence and **Horizon** for monitoring/throttling |
| Raw `mysql2` queries (`database/`, `functions/`) | **Eloquent ORM** with typed models, migrations generated from `import.sql`, and query builder for reporting aggregates |
| Static file serving (`client/public/media`, `meta-media`) | **Laravel Filesystem** abstraction — local disk for dev, S3-compatible object storage for production; signed URLs for private media |
| i18n JSON dictionaries (`languages/`) | Laravel localization (`lang/`) + database-backed translation editor for SuperAdmin-managed strings |
| License/plan-limit middleware | Laravel middleware + a `PlanLimitService` checking quotas (seats, channels, messages/month) per workspace on relevant routes |

### 6.3 Real-Time Event Mapping

Every Socket.IO client action and server push in `REALTIME_AND_ENGINES.md §1.2–1.3` maps to a Laravel construct:

| Socket.IO Concept | Laravel Equivalent |
|---|---|
| `socket.emit("message", {type, payload})` generic envelope | Dedicated REST endpoints for actions that mutate state (e.g., `POST /api/v1/inbox/send-message`), since most "client actions" in the legacy system are really RPC calls, not push events |
| `chat_list`, `load_conversation`, `push_new_msg`, `update_conversations`, `request_update_chat_list`, `request_update_opened_chat` | **Broadcast events** implementing `ShouldBroadcast`, fired from model observers/jobs (e.g., `MessageSent`, `ConversationUpdated`), delivered over a private channel `workspace.{workspaceId}` and consumed via Echo listeners in React |
| `update_labels`, `forward_message_done`, `translation_result`, `suggestion_result` | Either a direct JSON response to the triggering API call (translation/suggestion are synchronous enough) or a broadcast event if generated asynchronously in a queued job |
| `connection_ack` with JWT decode + role lookup | Reverb's built-in channel authorization callback (`routes/channels.php`), using the authenticated Sanctum user/agent to authorize `private-workspace.{id}` and route agents vs. owners |
| Presence tracking (agent online/offline) | Reverb **presence channels** (`presence-workspace.{id}.agents`), replacing custom socket registries |

### 6.4 Architectural Risk: Node-Only Protocol Libraries

Two subsystems in the legacy stack rely on libraries with **no mature PHP equivalent**:

1. **Baileys (`@whiskeysockets/baileys`)** — implements the full WhatsApp Web multi-device protocol (E2E encryption, QR pairing, persistent socket) in JavaScript only. There is no production-grade PHP port.
2. **Telegram MTProto client (`gramjs`)** — personal-number Telegram login (distinct from the simpler Bot API) also has no equivalent Laravel-native library; the closest PHP option (`MadelineProto`) is usable but heavy and still most naturally run as its own long-lived process.

**Recommendation:** keep a small, isolated **Node.js "Bridge" microservice** responsible *only* for these two long-lived-socket protocols, and treat it as an internal service the Laravel app calls — not a rewrite target. Laravel remains the system of record (MySQL, business logic, REST API, all other channels, billing, automation, CRM). The Bridge:
- Exposes a minimal internal REST API (`create_session`, `send_message`, `get_status`) authenticated with a shared service token.
- Pushes inbound events (new message, QR update, connection status) to Laravel via a signed internal webhook, which Laravel then persists and re-broadcasts to the frontend over Reverb — so the **frontend never talks to the Bridge directly**, preserving one real-time contract.
- Persists only ephemeral session credentials; all business data (messages, contacts, chats) lives in Laravel's MySQL, written through the same webhook path used by the Meta Cloud webhook, so the Inbox has one unified ingestion pipeline regardless of channel.

This keeps ~90% of the system (CRM, contacts, campaigns for Cloud API, automations, billing, admin, Instagram, Messenger) as pure Laravel, while isolating the ~10% that requires Node to a single, small, replaceable service. If the business is willing to drop unofficial "QR-scan" WhatsApp sessions entirely and support **Meta Cloud API only**, the Bridge service (and this entire risk) can be eliminated — this is a product decision worth raising explicitly with stakeholders (see §9, Open Question #1).

### 6.5 Multi-Tenancy & Data Isolation

- Every tenant-scoped table carries a `user_id` (workspace owner) foreign key, enforced via an Eloquent **global scope** (`WorkspaceScope`) applied to all tenant models, preventing cross-tenant leakage by default rather than relying on manual `WHERE` clauses as in the legacy raw-SQL code.
- Agents inherit their owner's `workspace_id`/`user_id` scope but are further restricted by assignment (`assigned_agent`) and permission flags.

---

## 7. Data Model

Source: 62-table schema in `DATABASE_SCHEMA.md`, imported from `import.sql`. Rebuild as versioned Laravel migrations (one migration per table minimum, plus follow-up migrations to add missing FKs/indexes not present in the legacy dump). Group by domain:

| Domain | Representative Tables | Notes for Laravel Rebuild |
|---|---|---|
| **User, Auth & RBAC** | `admin`, `user`, `agents`, `agent_chats`, `agent_task`, `g_auth`, `fcm_tokens` | Map to `admins`, `users`, `agents` Eloquent models; `g_auth` → Google OAuth via Laravel Socialite; `fcm_tokens` → polymorphic `push_tokens` table |
| **Live Inbox & Messaging** | `beta_chats`, `beta_conversation`, `chats`, `chat_tags`, `quick_reply`, `rooms` | Consolidate `chats`/`beta_chats` duplication from the legacy schema into one `conversations` + `messages` pair with a `channel` enum, if confirmed safe during migration audit |
| **WhatsApp Meta Cloud API** | `meta_api`, `meta_templet_media`, `templets`, `beta_campaign`, `beta_campaign_logs`, `broadcast`, `broadcast_log` | `templets`/`meta_templet_media` → `message_templates`; campaign + log pairs map cleanly to Eloquent one-to-many |
| **Baileys QR & Devices** | `auth`, `instance`, `qr_campaigns`, `qr_campaign_logs`, `warmers`, `warmer_script` | `auth` (session creds) stays owned by the Bridge service per §6.4, referenced by `instance_id` only — Laravel does not store raw session keys |
| **Contacts & CRM** | `phonebook`, `contact`, `wa_contacts` | Normalize `contact`/`wa_contacts` overlap into one `contacts` table with a `source` column, plus `custom_field_values` (EAV or JSON column) for per-workspace custom fields |
| **Flow Builder & Automations** | `flow`, `flow_data`, `flow_session`, `flow_templates`, `beta_flows`, `chatbot`, `beta_chatbot` | `flow_data` → node/edge JSON stored as `json` column with a validated schema; `flow_session` → active execution state with `current_node_id` + `variables` JSON |
| **Forms & Lead Gen** | `wa_forms`, `wa_form_submissions`, `contact_form`, `gen_links` | Straightforward Eloquent CRUD |
| **Voice & Calls** | `wa_call_bot`, `wa_call_broadcasts`, `wa_call_flows`, `wa_call_logs` | Deferred to Phase 5 (§2.2) |
| **Telegram / Instagram / Messenger** | `telegram_session`, `instagram_accounts`, `messenger_accounts` | Instagram/Messenger are pure Graph API (Laravel-native); Telegram session ownership depends on the Bot-API-only decision in §6.4/§9 |
| **Webhooks & External API** | `webhooks`, `webhook_logs`, `beta_api_logs` | `webhooks` → configurable inbound mapping rules; `beta_api_logs` → rate-limited API key usage log |
| **Plans, Orders & Billing** | `plan`, `orders`, `smtp` | `plan` drives the `PlanLimitService` quota checks referenced in §6.2 |
| **Theme, CMS & Public Pages** | `web_private`, `web_public`, `page`, `faq`, `testimonial`, `partners`, `chat_widget`, `mobile_app` | Minimal viable CMS in Phase 4–5 per §2.2 |

**Migration approach:** write a one-time Artisan command that reads `import.sql`, generates draft Laravel migration stubs per table (column types, defaults), and a data-migration seeder to import existing rows — reviewed table-by-table rather than trusted blindly, since several tables above show naming duplication (`chats` vs `beta_chats`, `contact` vs `wa_contacts`) that should be resolved, not carried forward as technical debt.

---

## 8. API Surface

The legacy system exposes **378 REST endpoints across 23 modules** (full inventory in `API_SPECIFICATION.md`). The Laravel rebuild will organize these as versioned, resourceful API groups:

| Module Group | Endpoint Count (legacy) | Laravel Route Prefix |
|---|---|---|
| User & Workspace | 73 | `/api/v1/user/*` |
| Admin & Platform | 62 | `/api/v1/admin/*` (SuperAdmin guard) |
| Agent & Multi-Seat | 29 | `/api/v1/agent/*` |
| Visual Flow Builder | 20 | `/api/v1/automations/*` |
| Inbox | 5 (core; inbox actions also flow through websocket RPC in legacy) | `/api/v1/inbox/*` |
| Meta Cloud Broadcasts | 14 | `/api/v1/broadcasts/*` |
| Baileys QR Sessions | 13 | `/api/v1/devices/qr/*` |
| Baileys QR Campaigns | 9 | `/api/v1/campaigns/qr/*` |
| Kanban CRM | 3 | `/api/v1/crm/*` |
| Contacts & Phonebook | 10 | `/api/v1/contacts/*` |
| Keyword Chatbot | 4 | `/api/v1/chatbot/*` |
| AI Assistants | 2 | `/api/v1/ai/*` |
| WhatsApp Voice & Calls | 19 | Deferred (Phase 5) |
| Dynamic Forms | 8 | `/api/v1/forms/*` |
| Telegram | 15 | `/api/v1/telegram/*` (scope reduced per §6.4) |
| Instagram DM | 6 | `/api/v1/instagram/*` |
| Facebook Messenger | 10 | `/api/v1/messenger/*` |
| Message Templates | 3 | `/api/v1/templates/*` |
| Theme & Landing CMS | 27 | Deferred (Phase 4–5) |
| Public Web Portal | 26 | `/api/v1/public/*` (unauthenticated) |
| Inbound Webhooks | 16 | `/api/v1/webhooks/*` |
| Developer API v1 | 4 | `/api/v1/dev/*` (API-key guarded, distinct from session auth) |

All endpoints get: consistent JSON envelope (`{data, meta, errors}`), Laravel **Form Request** validation classes (replacing legacy manual validation), API rate limiting via Laravel's throttle middleware, and OpenAPI/Swagger documentation generated from route annotations for the Developer Console (§5.8).

---

## 9. Non-Functional Requirements

| Category | Requirement |
|---|---|
| **Performance** | Inbox chat-list queries must return in < 300ms at 100k conversations per workspace (requires proper indexing on `workspace_id`, `updated_at`, `assigned_agent`; consider read replicas at scale) |
| **Real-time latency** | Inbound-message-to-UI-push under 1s end-to-end (webhook → job → broadcast → Echo) |
| **Availability** | Queue workers and Reverb run under Supervisor with auto-restart; Bridge service (§6.4) is a single point of failure for QR sessions and should run with process monitoring + session reconnect logic ported from the legacy `connection.update` handler |
| **Security** | Sanctum token auth, Spatie RBAC, encrypted-at-rest storage of channel credentials (Meta tokens, session keys) via Laravel's `encrypted` cast, signed webhook verification (Meta `X-Hub-Signature-256`, custom HMAC for the internal Bridge webhook) |
| **Multi-tenancy isolation** | Global Eloquent scopes (§6.5) plus automated tests asserting no cross-tenant data leakage on every tenant-scoped endpoint |
| **Auditability** | Retain the legacy pattern of `*_logs` tables (campaign, webhook, API) as append-only audit trails |
| **Internationalization** | Laravel `lang/` files + DB-backed override for SuperAdmin-managed translations, matching the legacy `languages/English.json` master dictionary approach |
| **Scalability of background work** | Campaign and warmer loops become Horizon-supervised queues with per-workspace rate limiting (Laravel's `Illuminate\Support\Facades\RateLimiter`) so one tenant's broadcast cannot starve others |

---

## 10. Success Metrics

- **Feature parity:** 100% of P0/P1 page capabilities (§4) available and passing QA against the legacy system's behavior before old system sunset.
- **Real-time reliability:** ≥ 99.5% of inbound messages reflected in the live inbox within 1 second.
- **Campaign delivery:** QR campaign anti-ban throttling keeps number ban rate at or below legacy system's observed baseline (establish baseline during Phase 3 QA).
- **Migration integrity:** 100% of rows from `import.sql` reconciled post-migration (row-count and checksum diff per table = 0 unexplained deltas).
- **API stability:** < 1% 5xx error rate on `/api/v1/*` in production, measured post-launch.

---

## 11. Rollout Plan / Phased Roadmap

| Phase | Scope | Pages/Modules |
|---|---|---|
| **Phase 1 — Foundations** | Laravel app scaffold, auth (Sanctum + RBAC), multi-tenancy scopes, migration of core schema, Bridge service skeleton, Reverb wired end-to-end with a "hello world" broadcast | Infra only |
| **Phase 2 — MVP Inbox & Channels** | Unified Inbox, WhatsApp Meta Cloud API, WhatsApp Devices (QR via Bridge), Contacts/Phonebook, CRM Kanban | Pages 1–5 |
| **Phase 3 — Growth Tools** | Campaigns & Broadcasts (both Cloud API and QR), Automations & Flow Builder, keyword chatbot, Team & Agents | Pages 6–8 |
| **Phase 4 — Platform & Ops** | Developer & Webhooks, Workspace Settings, minimal SuperAdmin back-office (tenants, plans, payments), Instagram/Messenger channels | Pages 9, 10 (partial), 11 |
| **Phase 5 — Long Tail** | Full landing CMS, Public Portal, WhatsApp Voice & Calls, Telegram MTProto (if approved), Number Warmer polish | Page 12, remainder of Page 10 |

Each phase ends with a parity QA pass against the legacy system's equivalent module before moving on, and Phase 2 onward runs the new system in parallel (shadow mode) against a subset of real tenant traffic before full cutover.

---

## 12. Open Questions / Decisions Needed

1. **Do we keep unofficial WhatsApp Web (Baileys/QR) sessions, or move to Meta Cloud API only?** This is the single biggest architecture and compliance decision — it determines whether the Node Bridge service (§6.4) exists at all. Unofficial sessions carry WhatsApp ban risk and ToS exposure; Cloud-API-only simplifies the entire stack to pure Laravel.
2. **Telegram: Bot API only, or also personal-number MTProto login?** Same trade-off pattern as #1, smaller blast radius.
3. **Do we migrate legacy data (`import.sql`) into the new schema, or launch clean and let tenants re-onboard?** Affects Phase timeline significantly — data migration requires the schema normalization noted in §7.
4. **Frontend strategy:** keep the existing React 19 + Vite SPA calling the new Laravel API (recommended, protects UI investment), or move to Laravel-native Inertia.js/Livewire for a tighter monolith? This PRD assumes the former.
5. **Hosting/infra for the Node Bridge service** (if retained): same box as Laravel via Supervisor, or a separate small service behind an internal network — needs a decision before Phase 1 infra work.
6. **MongoDB session backend:** legacy supports three backends for Baileys creds (MySQL/Mongo/local disk). Recommend standardizing on one (MySQL via the Bridge, or local disk with S3 backup) to reduce operational surface — confirm no active tenants depend on Mongo specifically.

---

## 13. Appendix

- **Source inventory:** 62 database tables (`DATABASE_SCHEMA.md`), 378 REST endpoints across 23 route modules (`API_SPECIFICATION.md`), 12 top-level pages (`ALL_PAGES.md`), real-time event registry and background engine specs (`REALTIME_AND_ENGINES.md`).
- **Suggested Laravel package shortlist:** `laravel/sanctum`, `laravel/reverb`, `laravel/horizon`, `spatie/laravel-permission`, `laravel/socialite` (Google OAuth), `laravel/scout` (if full-text contact/message search is needed at scale), `spatie/laravel-medialibrary` (media handling), `barryvdh/laravel-dompdf` or similar for conversation PDF export.
- **UI system carryover:** per `PRODUCT.md`, the design language (dual-tone typography, `#2a85ff` accent, light/dark themes, 64px header / 290px-80px collapsible sidebar) belongs to the existing React component library and does not need to change as part of this backend migration.
