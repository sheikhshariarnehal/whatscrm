# 03. WhatsApp Devices & Session Manager Specification (`/devices`)

## 1. Overview & Purpose
The Devices page manages all WhatsApp communication channels: **WhatsApp Web Baileys QR pairings**, **Official Meta Cloud API credentials**, and the **WhatsApp Number Warmer Engine**.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Devices & Channels                         [+ Connect New WhatsApp]  │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [Paired Sessions (QR)]  [Meta Cloud API]  [Number Warmer]  [Telegram & Social]   │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────────────────┐ ┌────────────────────────────────────────┐ │
│ │ 📱 Device: Sales Support Line                │ │ 📱 Device: Marketing Outreach Line     │ │
│ │ Phone: +1 (555) 019-2834                     │ │ Phone: +1 (555) 019-8821               │ │
│ │ Status: 🟢 Connected (Battery: 85%)          │ │ Status: 🟢 Connected (Battery: 92%)    │ │
│ │ Storage: MySQL Auth Table                    │ │ Storage: MySQL Auth Table              │ │
│ │ Messages Sent Today: 420 msgs                │ │ Messages Sent Today: 890 msgs          │ │
│ │ [ 🔄 Reconnect ] [ ⚙️ Warmer: Active ] [ 🗑️ ] │ │ [ 🔄 Reconnect ] [ ⚙️ Warmer: Idle ] [ 🗑️ ]│ │
│ └──────────────────────────────────────────────┘ └────────────────────────────────────────┘ │
│                                                                                              │
│ ┌──────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ ⚡ Meta Cloud API Setup Status: 🟢 Configured & Active                                    │ │
│ │ WABA ID: 109283746192837 | Phone ID: 981273645102938 | App ID: 781923401928374          │ │
│ │ Webhook URL: `https://app.whatscrm.com/api/webhook` [Copy] | Token Status: Valid         │ │
│ │ [ ⚙️ Edit Meta Credentials ] [ 🧪 Test Inbound Webhook ] [ 📄 Sync WhatsApp Templates ]   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **Paired Sessions (QR)**: Cards for each connected Baileys WhatsApp Web session with live connection badges, reconnect buttons, battery/network health, and disconnect options.
2. **Meta Cloud API Tab**: Form to configure Meta Graph API credentials (`token`, `phone_number_id`, `waba_id`, `app_id`, `webhook_token`) and webhook callback verification.
3. **Number Warmer Tab**: Peer-to-peer warmup control panel. Shows paired number matrix, daily message progression charts, script selector, and sleep delay timers.
4. **Telegram & Social Tab**: Telegram Bot API tokens, Telegram MTProto user client login with phone verification code, Instagram Business Account linkage, and Facebook Messenger Page tokens.

## 4. Modals & Dialogs
- **QR Code Pairing Modal**: Opens live WebSocket session. Displays auto-refreshing QR code with pulsing animation. Detects scanning in real-time and transitions to success view.
- **Number Warmer Settings Modal**: Sets minimum/maximum sleep intervals (e.g. 5–15 seconds), max daily messages per number, and custom conversation dialog scripts.
- **Meta Credentials Modal**: Secure input of Meta Permanent System User Token with test ping validation.
