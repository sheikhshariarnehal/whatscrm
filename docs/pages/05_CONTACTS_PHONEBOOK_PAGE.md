# 05. Contacts & Phonebook Specification (`/contacts`)

## 1. Overview & Purpose
The Contacts page manages the customer database, phonebook groups, custom fields, contact tags, and high-volume CSV import/export operations.

## 2. Layout & Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: 🏢 Acme Corp / Contacts & Phonebook       [+ Add Contact]  [📥 Import CSV]  [📤 Export]│
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ Sub-Navbar: [All Contacts (2,450)]  [Contact Groups (12)]  [Custom Fields]  [Blacklist]      │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ 🔍 [Search name, phone, tag...]  [Group: All Groups ▾]  [Tag: All Tags ▾]  [Bulk Actions ▾] │ │
│ ├──────────────────────────────────────────────────────────────────────────────────────────┤ │
│ │ [x] Name          Phone Number    Group         Tags            Created At    Actions    │ │
│ │ [x] Jane Doe      +1 555-0199     VIP Retail    [VIP] [Lead]    2026-03-12    [💬] [✏️] [🗑️]│ │
│ │ [ ] John Smith    +1 555-0182     Wholesale     [Wholesale]     2026-03-10    [💬] [✏️] [🗑️]│ │
│ │ [ ] Alice Brown   +44 7911 1234   E-Commerce    [Cart Abandon]  2026-03-08    [💬] [✏️] [🗑️]│ │
│ │ [ ] Carlos Silva  +55 11 9876     Retail Latam  [Latam]         2026-03-01    [💬] [✏️] [🗑️]│ │
│ ├──────────────────────────────────────────────────────────────────────────────────────────┤ │
│ │ Showing 1-25 of 2,450 contacts                         [<< Previous] [1] [2] [3] [Next >>] │ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## 3. Sub-Tabs & Views
1. **All Contacts**: Paginated data table with search, multiple tag filtering, group selector, single-click chat trigger, edit, and delete.
2. **Contact Groups**: Group cards showing member counts, group description, broadcast campaign history, and bulk group actions.
3. **Custom Fields**: Dynamic field manager (e.g. `company_name`, `city`, `order_id`, `customer_tier`) used for template variable replacements in broadcasts.
4. **Blacklist / Opt-Out**: Contacts who have unsubscribed or opted out of automated campaigns.

## 4. Modals & Workflows
- **CSV Bulk Import Wizard**: 3-step import flow:
  1. Upload CSV file.
  2. Map CSV columns to CRM fields (`Name`, `Phone`, `Custom_Var1`, etc.).
  3. Select target Contact Group and execute import with live progress indicator.
- **Add / Edit Contact Modal**: Manual single contact creation with phone number country code validator.
