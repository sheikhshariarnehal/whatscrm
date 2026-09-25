# 09. Developer, Webhooks & API Specification (`/developer`)

## 1. Overview & Purpose
The Developer page equips developers and technical integrators with tools to connect external e-commerce systems (WooCommerce, Shopify, Custom CRM) via **Inbound Webhooks**, generate **API Keys** for the REST API v1, and inspect real-time payload logs.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Developer & Integrations                   [+ Create New Webhook]    │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [Inbound Webhooks (4)]  [API Keys]  [Webhook Logs]  [Interactive API Docs]       │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ Webhook Name   Target Event          Endpoint URL                                 Status │ │
│ ├──────────────────────────────────────────────────────────────────────────────────────────┤ │
│ │ WooCommerce    Order Placed / Paid   `https://app.whatscrm.com/api/webhook/IGaUVF` 🟢 Active │ │
│ │ Shopify Store  Cart Abandoned        `https://app.whatscrm.com/api/webhook/X6TgmQ` 🟢 Active │ │
│ │ Custom CRM     Lead Form Submitted   `https://app.whatscrm.com/api/webhook/0Eh4gm` 🟢 Active │ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **Inbound Webhooks Tab**: Manage webhook listener URLs (`webhooks` table). Map incoming JSON payloads to WhatsApp message templates.
2. **API Keys Tab**: Generate and revoke REST API tokens (`apiv2.js` / `/api/v1`) to send programmatic messages via cURL / Node / Python.
3. **Webhook Logs Tab**: Real-time inspector for received webhook payloads, showing raw JSON body, HTTP headers, timestamp, and triggered action status.
4. **Interactive API Docs Tab**: Built-in Swagger/OpenAPI interactive console for developers to test sending messages and fetching contacts.
