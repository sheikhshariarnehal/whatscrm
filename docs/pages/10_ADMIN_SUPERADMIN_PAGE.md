# 10. SuperAdmin & SaaS Management Specification (`/admin`)

## 1. Overview & Purpose
The SuperAdmin page is the master back-office for the SaaS platform owner. It provides complete control over tenant user accounts, subscription pricing plans, payment gateway keys, SMTP email servers, master translations, landing page CMS, and global system health.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🛡️ WhatsCRM SuperAdmin Portal                                [System Status: 🟢 OK]   │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [Users]  [SaaS Plans]  [Orders & Revenue]  [Gateways]  [Translations]  [Settings]│
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐          │
│ │ 👥 Total Tenants │ │ 💰 Monthly ARR   │ │ 📱 Active Devices│ │ ✉️ Total Broadcast│          │
│ │   1,420 Users    │ │   $24,500 / mo   │ │   3,890 Sessions │ │   2.4M Messages  │          │
│ └──────────────────┘ └──────────────────┘ └──────────────────┘ └──────────────────┘          │
│                                                                                              │
│ ┌──────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ Tenant Management: [Search user / email...] [Filter Plan: All ▾] [Status: Active ▾]       │ │
│ ├──────────────────────────────────────────────────────────────────────────────────────────┤ │
│ │ User Name        Email               Plan        Devices  Expires On    Status   Actions │ │
│ │ Acme Corp        admin@acme.com      Pro Plan    4 / 5    2026-12-31    🟢 Active [🔑] [✏️]│ │
│ │ Global Media     contact@global.com  Enterprise  12 / 20  2027-06-30    🟢 Active [🔑] [✏️]│ │
│ │ Starter Shop     owner@starter.io    Free Trial  1 / 1    2026-04-05    🟡 Trial  [🔑] [✏️]│ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **Users & Tenants Tab**: Full SaaS user management: edit quota limits, reset passwords, change subscription plans, suspend accounts, and login-as-user (impersonation).
2. **SaaS Subscription Plans Tab**: Create and edit pricing tiers (`plan` table) with quota limits (Max WhatsApp Numbers, Max Broadcast Contacts, AI Credit Limits, Team Agent Seats, Price, Billing Interval).
3. **Orders & Revenue Tab**: Financial transaction ledger for subscriptions processed through Stripe, MercadoPago, Razorpay, or offline manual bank transfers.
4. **Payment Gateways Tab**: Configure API secret keys and webhooks for Stripe, MercadoPago, PayPal, and offline payment instructions.
5. **Language & Translations Editor**: Live editor for `English.json` and other localized dictionaries with search and inline translation.
6. **SMTP & Email Settings**: Mail server credentials (`smtp` table) for welcome emails, password resets, and subscription invoices.
7. **Landing Page CMS & Theme Tab**: Configure homepage hero banner, feature sections, testimonial reviews, pricing tables, and footer links.
