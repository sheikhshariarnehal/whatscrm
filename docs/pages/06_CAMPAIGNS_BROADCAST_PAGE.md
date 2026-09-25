# 06. Campaigns & Mass Broadcasts Specification (`/campaigns`)

## 1. Overview & Purpose
The Campaigns page is the mass outreach hub. It allows businesses to schedule and execute high-throughput broadcast campaigns through both **Official WhatsApp Meta Cloud API** and **Paired WhatsApp Web (Baileys QR) Numbers** with anti-ban delay throttling, dynamic CSV variable interpolation, and real-time delivery tracking.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Broadcast Campaigns                        [+ Create New Campaign]   │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [All Campaigns (18)]  [Create Campaign]  [Message Templates]  [Delivery Logs]    │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ Campaign Name       Channel    Recipients  Delivered   Failed  Status      Date     Action   │ │
│ ├──────────────────────────────────────────────────────────────────────────────────────────┤ │
│ │ Spring Promo 2026   Meta API   5,000       4,920 (98%) 80      🟢 Done     Mar 24   [📊 Logs]│ │
│ │ VIP Flash Sale      QR Device  1,200       1,180 (98%) 20      🟢 Done     Mar 20   [📊 Logs]│ │
│ │ Cart Recovery Wave  Meta API   450         320 (71%)   12      🟡 Sending  Mar 25   [⏸️ Pause]│ │
│ │ Product Launch Q2   QR Device  3,500       0           0       ⏳ Scheduled Apr 01  [✏️ Edit] │ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **All Campaigns Tab**: Real-time list of all campaigns with progress bars, channel badges (Meta Cloud vs QR Session), sent/delivered/failed ratios, and status actions (Pause, Resume, Abort, View Report).
2. **Create Campaign Wizard**:
   - **Step 1**: Campaign Title & Channel Selection (Meta Cloud API vs WhatsApp Web QR numbers).
   - **Step 2**: Recipient Selection (Select Phonebook Group or Upload custom CSV).
   - **Step 3**: Message Crafting (Select Meta Template or compose Rich Text + Media).
   - **Step 4**: Variable Mapping (Map `{{1}}` to Name, `{{2}}` to Promo Code).
   - **Step 5**: Pacing & Schedule (Set sleep intervals 8–15s for QR numbers, choose Immediate or Scheduled time).
3. **Message Templates Tab**: Approved Meta Cloud WhatsApp templates synced directly from Meta Graph API.
4. **Delivery Logs Tab**: Granular per-contact delivery logs (`sent`, `delivered`, `read`, `failed`, `error_reason`) with CSV export.

## 4. API Endpoints
- `POST /api/broadcast/create_broadcast` (Meta Cloud campaign)
- `POST /api/qr_campaign/create_qr_campaign` (QR Web campaign)
- `GET /api/broadcast/get_broadcast_logs` & `GET /api/qr_campaign/get_qr_campaign_logs`
- `GET /api/templet/get_templets` (Meta approved templates)
