# 🗂️ Epics

This folder breaks the [Complete API specification](../Complete_APIs.md) into delivery-ready epics. Each epic groups related APIs into features and user stories with acceptance criteria, tasks, and a definition of done.

| Epic | Theme | APIs Covered |
| :--: | ----- | ------------ |
| [01 — Meter Management](epic-01-meter-management.md) | Onboard, calibrate, and retire meters | Register Meter · Bulk Import · Upload Calibration · Deactivate Meter |
| [02 — Readings & Consumption](epic-02-readings-and-consumption.md) | Capture, correct, and surface consumption | Ingest Readings · Correct Reading · Query Consumption · Reading Telemetry · Detect Negative Consumption |
| [03 — Tariff & Billing](epic-03-tariff-and-billing.md) | Pricing, simulation, invoicing, export | Create Tariff · Simulate Tariff · Manage Simulations · Run Billing Job · Export Billing CSV |
| [04 — Reconciliation & Settlement](epic-04-reconciliation-and-settlement.md) | Match payments and settle participants | Import Bank Statement · Manual Reconciliation Match · Generate Settlement Summary |
| [05 — Reporting, Audit & Integration](epic-05-reporting-audit-and-integration.md) | Compliance, auditability, webhooks | Export Regulatory Report · Get Audit Trail · Subscribe Webhooks |

All 20 APIs from the source specification are covered across these five epics.

## 🔁 Shared Conventions

These cross-cutting requirements apply to every epic and underpin each story's acceptance criteria:

- **Auth** — Bearer token via [Laravel Sanctum](https://laravel.com/docs/sanctum). The `energy_conversion.write` scope referenced in stories maps to a Sanctum **token ability**. Missing/insufficient ability → `403`; unauthenticated → `401`.
- **Organization context** — every request carries an `organizationId`; access is scoped to it.
- **Idempotency** — mutating endpoints honor the `Idempotency-Key` header and must not double-process.
- **Side effects** — successful mutations emit a domain event and write an audit record.

## 📐 Epic Structure

Every epic doc follows the same right-sized template — only the sections that earn their place for this API:

- **🔍 Overview** — goal, business/user value, success metric, out of scope.
- **🧩 Features → User Stories** — each with **Acceptance Criteria** and **Tasks**.
- **🏗️ Design Patterns** — patterns *actually applied* (with justification) plus a **Deferred** table listing candidates and the concrete trigger that would justify them.
- **🧪 Testing** — Pest feature tests mapped one-to-one to acceptance criteria, plus a focused edge-cases table. No blanket coverage mandates, E2E/UI rituals, or external coverage-gate tooling.
- **✅ Definition of Done**.

## 🧱 Design Patterns

The project uses a **domain-oriented modular monolith** (see the [main README](../../README.md#-architecture)), but **convention-first is the rule**: reach for plain Laravel before any custom abstraction.

### Default approach (use this first)

- **Controller** (thin) → **Form Request** (validation + `authorize()` for the Sanctum ability) → **Eloquent** → **API Resource** (response envelope).
- Idempotency for mutating endpoints via a single shared middleware (`Idempotency-Key`).
- Audit + cross-domain reactions via Laravel **events/listeners** — only where a mutation actually needs them. Reads never emit events.

This covers the majority of the 20 endpoints with no extra layers.

### Applied vs. Deferred

Each epic's **🏗️ Design Patterns** section is split in two:

- **Applied** — patterns the epic genuinely uses today, each with a justification.
- **Deferred** — heavier building blocks (Dedicated Action, Strategy, Pipeline, Chain, Queued Batch, Builder, Adapter, State machine, DTO…) listed with the **trigger** that must be true before adopting them. Per the Engineering Standards (YAGNI, prefer framework conventions, justify every abstraction), do **not** build a deferred pattern preemptively.

See the [main README](../../README.md) and [Complete API specification](../Complete_APIs.md) for request/response envelopes and full schemas.
