# 08. Team & Agent Management Specification (`/team`)

## 1. Overview & Purpose
The Team page allows workspace owners to invite team agents, define role-based access permissions (RBAC), track agent spend time in conversations, and assign internal tasks.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Team & Agents                              [+ Invite New Agent]      │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [Team Members (6)]  [Agent Tasks (14)]  [Performance Metrics]  [Roles & Permissions]│
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ Agent Name      Email               Role           Active Chats  Status    Time Today Actions│ │
│ ├──────────────────────────────────────────────────────────────────────────────────────────┤ │
│ │ Sarah Miller    sarah@acme.com      Senior Agent   14 chats      🟢 Online 4h 20m    [✏️] [🗑️]│ │
│ │ David Kim       david@acme.com      Support Rep    8 chats       🟢 Online 3h 45m    [✏️] [🗑️]│ │
│ │ Alex Rivera     alex@acme.com       Sales Rep      12 chats      🟡 Away   2h 10m    [✏️] [🗑️]│ │
│ │ Emma Watson     emma@acme.com       Junior Rep     3 chats       ⚪ Offline 0h 00m   [✏️] [🗑️]│ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **Team Members Tab**: List of all sub-agents with live presence indicators (Online/Away/Offline), active conversation count, daily logged hours, edit permissions, and revoke access.
2. **Agent Tasks Tab**: Internal task tracking board (`agent_task` table) where managers assign follow-up tasks to specific agents with due dates and completion status.
3. **Performance Metrics Tab**: Agent analytics showing Average Response Time, Resolution Time, Total Messages Sent, and Customer Satisfaction ratings.
4. **Roles & Permissions Tab**: Granular permission matrix controlling what agents can see (e.g. Can view only assigned chats vs all chats, Can export contacts, Can delete conversations, Can launch broadcasts).

## 4. API Endpoints
- `GET /api/agent/get_agents` & `POST /api/agent/create_agent`
- `POST /api/agent/update_agent` & `DELETE /api/agent/delete_agent`
- `GET /api/agent/get_agent_tasks` & `POST /api/agent/add_task`
