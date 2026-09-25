# AGENTS.md — WhatsApp CRM Development Guide

## 1. Project Overview

This repository is a **WhatsApp CRM SaaS**.

The frontend is based on the existing **React + Vite dashboard template** in `starter/`. The goal is to preserve and extend the template's visual system while connecting it to a Laravel backend and the project's existing architecture, database, realtime, and WhatsApp systems.

### Core direction

- **Frontend:** React + Vite + JavaScript/TypeScript as already established by the template
- **Backend:** Laravel
- **Database:** Use the database design documented in `database/` and `DATABASE_SCHEMA.md`
- **Realtime:** Follow `REALTIME_AND_ENGINES.md`
- **API:** Follow `API_SPECIFICATION.md`
- **Overall architecture:** Follow `ARCHITECTURE.md`
- **Product requirements:** Follow `WhatsCRM_Laravel_PRD.md`
- **UI:** Reuse the existing React dashboard template; do not redesign the visual language unnecessarily
- **WhatsApp:** Use the official Meta WhatsApp Business/Cloud API architecture defined by the project documentation

---

# 2. Repository Structure

Expected project structure:

```text
WhatsApp CRM/
│
├── database/
│   └── Database-related files, migrations, schema references, SQL, etc.
│
├── docs/
│   └── Project documentation
│
├── starter/
│   ├── public/
│   ├── src/
│   │   ├── assets/
│   │   ├── auth/
│   │   ├── components/
│   │   ├── configs/
│   │   ├── constants/
│   │   ├── locales/
│   │   ├── mock/
│   │   ├── services/
│   │   ├── store/
│   │   ├── utils/
│   │   ├── views/
│   │   ├── App.jsx
│   │   ├── index.css
│   │   └── main.jsx
│   ├── index.html
│   ├── package.json
│   ├── vite.config.js
│   └── ...
│
├── API_SPECIFICATION.md
├── ARCHITECTURE.md
├── DATABASE_SCHEMA.md
├── REALTIME_AND_ENGINES.md
├── WhatsCRM_Laravel_PRD.md
└── AGENTS.md
```

If additional documentation or source files exist, inspect them before changing related functionality.

---

# 3. Source of Truth Priority

When information conflicts, use this order:

1. Explicit user instruction in the current task
2. `WhatsCRM_Laravel_PRD.md`
3. `ARCHITECTURE.md`
4. `DATABASE_SCHEMA.md`
5. `API_SPECIFICATION.md`
6. `REALTIME_AND_ENGINES.md`
7. Existing implementation
8. Comments or assumptions

Do not invent a new architecture when the project documentation already defines one.

If documentation and implementation disagree:

- identify the conflict
- avoid silently changing the documented contract
- update the relevant documentation if the new behavior is intentionally approved

---

# 4. Frontend Development Rules

## 4.1 Preserve the Existing Dashboard Design

The `starter/` directory contains the selected React dashboard template.

The existing UI is the foundation of the WhatsApp CRM frontend.

When implementing CRM features:

- reuse existing components
- reuse existing layout components
- reuse existing spacing
- reuse existing typography
- reuse existing colors/tokens
- reuse existing buttons
- reuse existing forms
- reuse existing tables
- reuse existing modals/drawers
- reuse existing navigation/sidebar patterns
- reuse existing responsive behavior
- reuse existing icon conventions

Do **not** replace the template with a completely different UI system unless explicitly requested.

The objective is:

```text
Existing React Dashboard
        +
WhatsApp CRM functionality
        =
WhatsApp CRM product
```

Not:

```text
Existing React Dashboard
        ↓
Throw away design
        ↓
Build unrelated UI
```

---

# 5. Frontend Architecture

Keep responsibilities separated.

Recommended structure:

```text
src/
├── assets/
├── auth/
├── components/
│   ├── common/
│   ├── layout/
│   ├── ui/
│   └── whatsapp/
├── configs/
├── constants/
├── locales/
├── mock/
├── services/
│   ├── api/
│   ├── auth/
│   ├── contacts/
│   ├── conversations/
│   ├── messages/
│   ├── campaigns/
│   └── whatsapp/
├── store/
├── utils/
├── views/
│   ├── dashboard/
│   ├── inbox/
│   ├── contacts/
│   ├── campaigns/
│   ├── templates/
│   ├── automation/
│   ├── teams/
│   ├── analytics/
│   └── settings/
├── App.jsx
├── index.css
└── main.jsx
```

Adapt this structure to the existing template instead of blindly creating duplicate folders.

---

# 6. Laravel Integration

The React application communicates with Laravel through APIs.

Conceptually:

```text
React/Vite
    │
    │ HTTPS REST API
    ▼
Laravel
    │
    ├── Authentication
    ├── CRM business logic
    ├── WhatsApp integration
    ├── Webhooks
    ├── Campaigns
    ├── Automations
    └── Permissions
    │
    ▼
Database / Redis / Workers
```

The frontend must never directly access:

- database credentials
- Redis credentials
- Meta access tokens
- Laravel private configuration
- server secrets

Use environment variables for public frontend configuration only.

---

# 7. API Rules

The API contract is defined by `API_SPECIFICATION.md`.

Before creating a new API call:

1. Check whether the endpoint already exists.
2. Check its request format.
3. Check its response format.
4. Check authentication requirements.
5. Check pagination/filtering rules.
6. Check error handling.
7. Reuse existing service functions when possible.

Do not create multiple frontend implementations of the same API.

Recommended pattern:

```text
View
 ↓
Feature Hook / Service
 ↓
API Client
 ↓
Laravel API
```

Avoid putting large API calls directly inside UI components.

---

# 8. Authentication

Authentication must follow the project's Laravel authentication design.

The frontend should maintain:

- authenticated user
- organization/workspace
- permissions
- session state
- loading state
- authentication errors

Do not store sensitive credentials in localStorage unless explicitly required by the documented architecture.

Handle:

- login
- logout
- session expiration
- unauthorized API responses
- organization selection if supported
- permission-based UI

---

# 9. Multi-Tenant SaaS

The application is intended to support multiple businesses/organizations.

Always consider tenant isolation.

Conceptually:

```text
Organization
│
├── Users
├── WhatsApp Accounts
├── Contacts
├── Conversations
├── Messages
├── Campaigns
├── Templates
├── Tags
├── Automations
└── Settings
```

Frontend code should never assume that data belongs to a single global organization.

Use the organization/workspace context defined by the backend.

Never expose another organization's:

- contacts
- messages
- campaigns
- WhatsApp numbers
- templates
- analytics
- settings

---

# 10. WhatsApp Architecture

Use the official WhatsApp API architecture defined by the project documentation.

Incoming messages:

```text
WhatsApp
   ↓
Meta Webhook
   ↓
Laravel
   ↓
Validate / Process
   ↓
Database
   ↓
Realtime event
   ↓
React Inbox
```

Outgoing messages:

```text
React
   ↓
Laravel API
   ↓
Validate / Authorize
   ↓
Queue
   ↓
Worker
   ↓
Meta WhatsApp API
   ↓
Message status webhook
   ↓
Laravel
   ↓
Database
   ↓
Realtime
   ↓
React
```

Do not implement the CRM around WhatsApp Web browser automation when the official Cloud API is available for the required functionality.

---

# 11. Realtime System

Follow `REALTIME_AND_ENGINES.md`.

Realtime functionality may include:

- new incoming messages
- sent message updates
- delivery status
- read status
- typing indicators if supported
- conversation assignment
- unread count
- campaign progress
- automation events

Do not repeatedly poll the server for data that is intended to be realtime.

Keep realtime event names and payloads consistent with the documented backend contract.

---

# 12. Background Jobs

Long-running or high-volume operations must not block normal HTTP requests.

Examples:

- bulk campaign processing
- WhatsApp message sending
- message retries
- media processing
- scheduled messages
- automation execution
- analytics aggregation
- webhook-heavy processing

Preferred flow:

```text
HTTP Request
    ↓
Create/validate operation
    ↓
Queue job
    ↓
Return response
    ↓
Worker processes job
```

Use the Laravel queue/Redis architecture defined by the project.

---

# 13. Database Rules

`DATABASE_SCHEMA.md` and the `database/` directory are the primary references for data structure.

Before adding a table/column:

1. Check the existing schema.
2. Check whether the field already exists.
3. Check relationships.
4. Check tenant ownership.
5. Check indexes.
6. Check whether migrations are required.
7. Update documentation if the schema changes.

Do not create duplicate concepts such as:

```text
customer
contact
lead
client
```

without confirming how the project defines each entity.

---

# 14. CRM Core Modules

The frontend should be organized around the product modules defined in the PRD.

Expected modules include:

```text
Dashboard
Inbox
Contacts
Conversations
Campaigns
Templates
Automations
Teams
Analytics
WhatsApp Accounts
Settings
Billing
```

Only implement modules that are supported by the current PRD/architecture.

---

# 15. Inbox UX

The Inbox is a core feature.

A typical layout:

```text
┌─────────────────────────────────────────────────────┐
│ Search / Filters                                    │
├──────────────┬────────────────────────┬─────────────┤
│ Conversations│        Chat            │ Customer    │
│              │                        │ Details     │
│ Contact 1    │ Messages               │             │
│ Contact 2    │                        │ Name        │
│ Contact 3    │                        │ Phone       │
│              │ Message composer       │ Tags        │
└──────────────┴────────────────────────┴─────────────┘
```

Use the existing dashboard components to implement this.

Important UX considerations:

- unread state
- message status
- timestamps
- attachments
- customer information
- tags
- assignment
- search
- conversation filters
- responsive layout
- loading states
- empty states
- error states

---

# 16. Mock Data

The existing `mock/` directory may contain template/demo data.

Mock data is acceptable during UI development.

However:

- clearly separate mock services from production API services
- do not accidentally ship fake data as production data
- do not hard-code customer information
- replace mocks progressively as backend APIs become available

Recommended:

```text
Mock API
    ↓
Same frontend service interface
    ↓
Real Laravel API
```

This allows UI development before every backend endpoint is complete.

---

# 17. State Management

Use the existing `store/` architecture where appropriate.

Separate:

### Server state

Examples:

- contacts
- conversations
- messages
- campaigns
- templates

### Client/UI state

Examples:

- sidebar open/closed
- selected conversation
- modal visibility
- filters
- temporary form state

Do not put every API response into global state unnecessarily.

---

# 18. Performance

The CRM can eventually contain a large number of conversations and messages.

Use:

- pagination
- cursor pagination where documented
- lazy loading
- virtualized message lists when necessary
- debounced search
- server-side filtering
- indexed database queries
- caching where appropriate

Avoid loading thousands of messages or contacts into the browser at once.

---

# 19. Security

Never expose:

```text
META_ACCESS_TOKEN
DATABASE_PASSWORD
REDIS_PASSWORD
APP_KEY
PRIVATE_API_KEYS
WEBHOOK_SECRETS
```

to the React client.

Validate permissions on the Laravel backend even if the frontend hides unauthorized controls.

Frontend permission checks are for UX.

Backend permission checks are for security.

---

# 20. Error Handling

Every production API interaction should have:

- loading state
- success state
- empty state
- error state

Do not silently swallow errors.

For example:

```text
Loading...
No conversations
Failed to load conversations
Retry
```

Use the template's existing notification/toast system if available.

---

# 21. Coding Style

Before creating new code:

1. Inspect nearby existing code.
2. Follow existing naming conventions.
3. Reuse existing utilities.
4. Reuse existing components.
5. Avoid unnecessary dependencies.
6. Keep components focused.
7. Avoid giant components.
8. Keep API logic outside presentation components.
9. Keep business logic out of purely visual components.

Prefer incremental changes over large rewrites.

---

# 22. Documentation Rules

The project already contains documentation:

```text
API_SPECIFICATION.md
ARCHITECTURE.md
DATABASE_SCHEMA.md
REALTIME_AND_ENGINES.md
WhatsCRM_Laravel_PRD.md
```

Treat these as living documents.

When implementing a meaningful architectural change, update the relevant documentation.

Examples:

| Change | Update |
|---|---|
| New API endpoint | `API_SPECIFICATION.md` |
| Architecture change | `ARCHITECTURE.md` |
| DB/table change | `DATABASE_SCHEMA.md` |
| Realtime event change | `REALTIME_AND_ENGINES.md` |
| Product behavior change | `WhatsCRM_Laravel_PRD.md` |

---

# 23. Existing System Documentation

The `database/` directory and existing project documents may contain previous work, schema definitions, SQL, design decisions, and system information.

Before replacing an existing system:

1. Inspect the relevant files.
2. Understand the existing behavior.
3. Preserve compatible functionality.
4. Migrate deliberately.
5. Document breaking changes.

Do not delete existing database/system files simply because a new implementation is being created.

---

# 24. Development Workflow

For each feature:

```text
1. Read relevant documentation
        ↓
2. Inspect existing implementation
        ↓
3. Understand data/API requirements
        ↓
4. Design the smallest compatible change
        ↓
5. Implement backend/API if required
        ↓
6. Implement React UI using existing template
        ↓
7. Connect real API
        ↓
8. Add loading/error/empty states
        ↓
9. Test
        ↓
10. Update documentation
```

---

# 25. Important Rule for AI Coding Agents

Before making changes, the agent should inspect:

```text
starter/src/
database/
API_SPECIFICATION.md
ARCHITECTURE.md
DATABASE_SCHEMA.md
REALTIME_AND_ENGINES.md
WhatsCRM_Laravel_PRD.md
```

when the task is related to those areas.

Do not guess the project's existing architecture.

Do not replace working systems without first understanding them.

Do not create duplicate implementations when an existing service/component already performs the job.

---

# 26. Definition of Done

A feature is not complete merely because the UI renders.

For a production feature, verify:

- UI matches the existing dashboard design
- API contract is correct
- authentication works
- tenant isolation works
- loading state exists
- empty state exists
- error state exists
- permissions are enforced
- database changes are documented
- realtime behavior works if required
- background jobs are queued if required
- no secrets are exposed
- existing functionality remains intact

---

# 27. Current Product Direction

The current development direction is:

```text
Existing React/Vite Dashboard Template
                +
Laravel Backend
                +
Database / existing database system
                +
Redis / Queue Workers
                +
Official WhatsApp Cloud API
                +
Realtime Engine
                =
Production WhatsApp CRM SaaS
```

The React template is the **visual foundation**.

Laravel is the **business/backend foundation**.

The existing project documentation is the **architecture source of truth**.

The `database/` directory is part of the existing system and must be considered before database-related changes.

