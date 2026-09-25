# WhatsCRM Master Page-by-Page Specifications & Wireframes Suite

This document indexes the complete page-by-page specifications, wireframes, component hierarchies, sub-tabs, API endpoints, and real-time WebSocket interactions for all 12 core application pages in WhatsCRM.

---

## 📑 Complete Index of Page Specifications

| # | Page Identifier | Route | Specification Document | Description |
| :--- | :--- | :--- | :--- | :--- |
| **01** | **Dashboard** | `/dashboard` | [01_DASHBOARD_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/01_DASHBOARD_PAGE.md) | Command center, message trend heatmap, channel split, and live incoming audit feed. |
| **02** | **Unified Live Inbox** | `/inbox` | [02_INBOX_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/02_INBOX_PAGE.md) | 3-panel omnichannel chat canvas (WhatsApp, Telegram, Instagram, Messenger) with voice notes, AI smart replies, and CRM sidebar. |
| **03** | **WhatsApp & Devices** | `/devices` | [03_DEVICES_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/03_DEVICES_PAGE.md) | Baileys QR Web session pairing, Meta Cloud API credentials, and Number Warmer peer matrix. |
| **04** | **CRM Pipeline** | `/crm` | [04_CRM_KANBAN_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/04_CRM_KANBAN_PAGE.md) | Interactive drag-and-drop Kanban pipeline with deal values, stages, and 1-click WhatsApp launcher. |
| **05** | **Contacts & Phonebook** | `/contacts` | [05_CONTACTS_PHONEBOOK_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/05_CONTACTS_PHONEBOOK_PAGE.md) | Contact database, group manager, custom field definitions, and 3-step CSV bulk import wizard. |
| **06** | **Campaigns & Broadcasts** | `/campaigns` | [06_CAMPAIGNS_BROADCAST_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/06_CAMPAIGNS_BROADCAST_PAGE.md) | Mass outreach wizard (Meta Cloud API & QR sessions), anti-ban sleep timers, template variable mapping, and live logs. |
| **07** | **Automations & Bots** | `/automations` | [07_AUTOMATIONS_FLOWS_BOTS_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/07_AUTOMATIONS_FLOWS_BOTS_PAGE.md) | Visual ReactFlow node graph builder, keyword auto-replies, AI Assistant personas (Gemini/OpenAI), and WA form builder. |
| **08** | **Team & Agents** | `/team` | [08_TEAM_AGENTS_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/08_TEAM_AGENTS_PAGE.md) | Multi-seat agent management, presence tracking, internal follow-up task board, and RBAC permissions. |
| **09** | **Developer & Webhooks** | `/developer` | [09_DEVELOPER_WEBHOOKS_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/09_DEVELOPER_WEBHOOKS_PAGE.md) | Inbound webhook listeners (WooCommerce/Shopify), API Keys, webhook payload debugger, and interactive API console. |
| **10** | **SuperAdmin Back-Office** | `/admin` | [10_ADMIN_SUPERADMIN_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/10_ADMIN_SUPERADMIN_PAGE.md) | Master SaaS control room: tenant users, pricing packages, payment gateways (Stripe/MercadoPago), translations, and CMS. |
| **11** | **Workspace Settings** | `/settings` | [11_SETTINGS_WORKSPACE_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/11_SETTINGS_WORKSPACE_PAGE.md) | Workspace profile, business logo, sound alerts, FCM push notifications, and active plan billing. |
| **12** | **Public Portal & Auth** | `/`, `/login` | [12_PUBLIC_PORTAL_AUTH_PAGE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/docs/pages/12_PUBLIC_PORTAL_AUTH_PAGE.md) | Customer-facing landing page, dynamic pricing grid, Google OAuth login, tenant registration, and password recovery. |

---

## 🧭 Navigation & Layout Architecture Reference
All application pages share the unified **Compact Icon Rail + Contextual Horizontal Sub-Navbar** layout defined in [DESIGN.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/DESIGN.md) and [FRONTEND_REBUILD_GUIDE.md](file:///o:/Template/WhatsCRM%20v6.1.0%20Nulled/CRM/FRONTEND_REBUILD_GUIDE.md).
