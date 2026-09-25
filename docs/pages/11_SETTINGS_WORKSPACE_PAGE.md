# 11. Workspace Settings Specification (`/settings`)

## 1. Overview & Purpose
The Settings page manages individual workspace configurations, profile details, security credentials, notification preferences, sound alerts, and billing subscriptions.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Workspace Settings                                                   │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [Profile & Company]  [Security & Password]  [Notifications]  [Billing & Plan]    │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ 🏢 Company & Profile Settings                                                            │ │
│ │ Company Name: [ Acme Corporation                     ]                                   │ │
│ │ Timezone:     [ (GMT-05:00) Eastern Time (US & Canada) ▾]                                 │ │
│ │ Default Country Code: [ +1 (United States) ▾ ]                                           │ │
│ │ Workspace Logo: [ Upload Image ] (PNG/JPG max 2MB)                                       │ │
│ │                                                                                          │ │
│ │ 🔔 Sound & Notification Preferences                                                      │ │
│ │ [x] Play sound alert on new incoming message                                             │ │
│ │ [x] Enable Browser Push Notifications (FCM)                                              │ │
│ │ [x] Send email digest when campaign finishes                                             │ │
│ │                                                                                          │ │
│ │ [ Save Changes ]                                                                         │ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **Profile & Company Tab**: Workspace title, business logo, default phone country code, and system timezone.
2. **Security & Password Tab**: Update account password, enable Two-Factor Authentication (2FA), and view active browser sessions.
3. **Notifications Tab**: Audio alert toggle, FCM push notifications, and email notification triggers.
4. **Billing & Plan Tab**: Current active plan details, expiration date, quota consumption bars, and upgrade plan button.
