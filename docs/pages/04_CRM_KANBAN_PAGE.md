# 04. CRM Pipeline & Kanban Deals Specification (`/crm`)

## 1. Overview & Purpose
The CRM page provides an interactive, drag-and-drop Kanban pipeline for managing sales leads, customer deal stages, deal values, lead tags, and automated stage movement workflows directly connected to WhatsApp conversations.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / CRM Pipeline               [+ New Deal]  [Pipeline: Sales 2026 ▾]    │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [Kanban Board]  [All Leads Table]  [Stage Settings]  [Pipeline Analytics]        │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐│
│ │ NEW LEADS (8)  │ │ CONTACTED (5)  │ │ PROPOSAL (3)   │ │ NEGOTIATION(2) │ │ WON / CLOSED(4)││
│ │ $12,400        │ │ $8,500         │ │ $15,000        │ │ $11,000        │ │ $28,000        ││
│ ├────────────────┤ ├────────────────┤ ├────────────────┤ ├────────────────┤ ├────────────────┤│
│ │ [Deal Card]    │ │ [Deal Card]    │ │ [Deal Card]    │ │ [Deal Card]    │ │ [Deal Card]    ││
│ │ Jane Doe       │ │ Acme Corp      │ │ Global Tech    │ │ Apex Logistics │ │ Bright Retail  ││
│ │ +1 555-0199    │ │ +1 555-4821    │ │ +44 20 7946    │ │ +1 555-9012    │ │ +1 555-3344    ││
│ │ $2,500         │ │ $4,000         │ │ $8,000         │ │ $6,000         │ │ $12,000        ││
│ │ [🔥 High] [WA] │ │ [Collab] [IG]  │ │ [Enterprise]   │ │ [Closing Fri]  │ │ [Paid] [VIP]   ││
│ │ Agent: Sarah   │ │ Agent: David   │ │ Agent: Sarah   │ │ Agent: Alex    │ │ Agent: Sarah   ││
│ └────────────────┘ └────────────────┘ └────────────────┘ └────────────────┘ └────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **Kanban Board Tab**: Horizontal drag-and-drop board with smooth physics using `@dnd-kit`. Columns represent deal stages with aggregate revenue headers.
2. **All Leads Table Tab**: Data table view of all CRM leads with multi-column sorting, search, bulk stage assignment, and CSV export.
3. **Stage Settings Tab**: Add, rename, reorder, delete, and color-code pipeline stages.
4. **Pipeline Analytics Tab**: Funnel conversion rates, stage drop-off analysis, deal velocity, and revenue projections.

## 4. Interactivity & Features
- **Drag-and-Drop Stage Transitions**: Dragging a deal card to a new column triggers `POST /api/kaban/update_stage` with optimistic UI update.
- **1-Click WhatsApp Chat Launch**: Clicking chat icon on any deal card navigates directly to that conversation in `/inbox`.
- **Add / Edit Deal Modal**: Form to input Contact Name, Mobile, Email, Deal Value, Expected Close Date, Priority (Low/Medium/High), Stage, and Assigned Agent.
