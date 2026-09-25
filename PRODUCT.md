# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Enterprises, SaaS administrators, e-commerce managers, marketing analysts, and customer success teams managing business operations, analytics, CRM workflows, product catalogs, and financial reports through a centralized web application.

## Product Purpose

Ecme is a modular, high-performance React dashboard and admin interface system designed to deliver clear data visualization, rapid CRUD operations, workflow automation, and consistent operational management across desktop and mobile browsers.

## Positioning

A clean, modern SaaS control center that balances high information density with exceptional visual clarity, featuring dual-tone typography, soft neutral surfaces, subtle rounded forms, and instant responsiveness.

## Operating Context

- **Primary Device**: Desktop browsers (1280px+ and 1536px+), tablet responsive, with adaptive drawer/mobile nav below 1024px.
- **Workflow Speed**: High-frequency operational tasks: scanning order status tables, filtering analytics by period, managing customer records, configuring workspace settings, and monitoring KPIs.
- **Environment**: Office and remote dashboard displays, supporting native Light and Dark modes.

## Capabilities and Constraints

- **Theme Engine**: Dual theme mode (Light & Dark) with dynamic preset theme schemas (Default Blue, Dark Minimal, Emerald Green, Purple, Orange).
- **Layout Variations**: Collapsible Side Navigation (default), Stacked Side Nav, Top Bar Classic, Frameless Side, Content Overlay, and Blank layouts.
- **Component Architecture**: Atomic, composable UI component library built on React 19, Tailwind CSS v4, and Floating UI.
- **State & Routing**: Zustand store for theme and user auth state; React Router 7 for nested view routing; TanStack React Table v8 for data grids; ApexCharts for analytics.

## Brand Commitments

- **Tone**: Focused, modern, dependable, and efficient.
- **Design Philosophy**: Functional minimalism with subtle tactile depth—no aggressive drop shadows or visual clutter.
- **Visual Identity**: Clean crisp whites (`#ffffff`) and soft tinted neutrals (`#f5f5f5`), paired with a distinctive Tech Electric Blue (`#2a85ff`) accent and duotone iconography.

## Evidence on Hand

- Active demo codebase: `o:\Template\Ecme v1.3.8 React\JavaScript\demo`
- Pre-built full dashboards: `EcommerceDashboard`, `AnalyticDashboard`, `MarketingDashboard`, `ProjectDashboard`.
- Complete UI component library with 40+ primitives in `src/components/ui/` and shared widgets in `src/components/shared/`.

## Product Principles

1. **Clarity First**: Data metrics, tables, and statuses must be immediately readable with standardized visual hierarchy.
2. **Predictable Layout Hierarchy**: Fixed 64px header, 290px/80px collapsible sidebar, and consistent page container gutters across all views.
3. **Cohesive Component Language**: Every button, input, card, badge, and modal shares identical corner radii, stroke weights, and interaction feedback.
4. **First-Class Dark Mode**: Dark surfaces use deep neutral grays (`#171717`, `#262626`) rather than pure black, maintaining contrast without eye fatigue.
