# 01. Dashboard Page Specification (`/dashboard`)

## 1. Overview & Purpose
The Dashboard is the command center for the workspace owner and agents. It provides a real-time summary of omnichannel messaging activity, active WhatsApp device sessions, campaign delivery rates, active Kanban leads, agent responsiveness, and quick action shortcuts.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Dashboard                       [Quick Action +]  [Filter: 7 Days ▾]  │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [Overview]  [Live Analytics]  [Recent Activity]  [Health & Quotas]              │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐          │
│ │ 💬 Total Messages│ │ 📱 Active Numbers│ │ 📢 Sent Broadcast│ │ 🎯 Open Deals    │          │
│ │   14,820 (+12%)  │ │   4 Connected    │ │   98.4% Delivered│ │   $42,500 (34)   │          │
│ └──────────────────┘ └──────────────────┘ └──────────────────┘ └──────────────────┘          │
│                                                                                              │
│ ┌───────────────────────────────────────────────┐ ┌────────────────────────────────────────┐ │
│ │ 📊 Message Volume & Response Heatmap (Chart)  │ │ 🌐 Channel Distribution                │ │
│ │ [24h / 7d / 30d Timeline of Inbound/Outbound] │ │ - WhatsApp (72%)  - Telegram (14%)     │ │
│ │                                               │ │ - Instagram (9%)  - Messenger (5%)     │ │
│ └───────────────────────────────────────────────┘ └────────────────────────────────────────┘ │
│                                                                                              │
│ ┌───────────────────────────────────────────────┐ ┌────────────────────────────────────────┐ │
│ │ ⚡ Live Feed & Recent Incoming Messages       │ │ 👥 Agent Performance Leaderboard       │ │
│ │ - [WA] Jane: "Need pricing quote" (2m ago)    │ │ - Sarah M. : 142 replies (Avg 1.2m)    │ │
│ │ - [TG] Alex: "Order #402 status"  (5m ago)    │ │ - David K. : 98 replies (Avg 2.4m)     │ │
│ │ - [IG] Beauty Co: "Collab request"(11m ago)   │ │ - Alex R.  : 84 replies (Avg 3.1m)     │ │
│ └───────────────────────────────────────────────┘ └────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **Overview Tab**: Key Metric Cards, Message Trend Graph, Channel Pie Chart, Quick Action Trigger Cards.
2. **Live Analytics Tab**: Detailed breakdown of inbound vs outbound messages, peak hours heatmap, average reply time, campaign throughput.
3. **Recent Activity Tab**: Live audit feed of messages sent, campaigns launched, QR device disconnects, and team agent logins.
4. **Health & Quotas Tab**: Plan usage metrics (WhatsApp numbers limit, message quota remaining, cloud storage used, AI credit tokens).

## 4. API Endpoints & Data Bindings
- `GET /api/user/get_user_analytics` (Daily, weekly, monthly message volume)
- `GET /api/user/get_user_info` (Current quota, active plan details, connected numbers)
- `GET /api/qr/get_instances` (List of active WhatsApp Web sessions)
- `GET /api/broadcast/get_campaigns_logs` (Broadcast success and failure counts)
- `GET /api/agent/get_agents` (Agent leaderboard and productivity)

## 5. WebSocket Real-Time Events
- `update_conversations`: Updates message counters live without page refresh.
- `push_new_msg`: Appends new item to live recent incoming activity feed.

## 6. Modals & Actions
- **Quick Broadcast Modal**: Rapid one-off message blast to selected phonebook group.
- **Add Device Modal**: Opens QR pairing modal immediately.
- **Export Analytics Modal**: Generates PDF/CSV performance summary.
