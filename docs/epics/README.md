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

## 🧱 Design Patterns

The project uses a **domain-oriented modular monolith** (see the [main README](../../README.md#-architecture)), but **convention-first is the rule**: reach for plain Laravel before any custom abstraction.

### Default approach (use this first)

Build each endpoint with built-in framework features:

- **Controller** (thin) → **Form Request** (validation + `authorize()` for the Sanctum ability) → **Eloquent** → **API Resource** (response envelope).
- Idempotency for mutating endpoints via a single shared middleware (`Idempotency-Key`).
- Audit + cross-domain reactions via Laravel **events/listeners** — but only where a mutation actually needs them.

This covers the majority of the 20 endpoints with no extra layers.

### Introduce more only when justified

Heavier building blocks are **optional and conditional** — add one only when a specific story's complexity makes its value clear and immediate (per the Engineering Standards: YAGNI, prefer framework conventions, justify every abstraction):

| Building block | Adopt only when… |
| -------------- | ---------------- |
| **Dedicated Action class** | A use case has real orchestration beyond a simple Eloquent write; trivial CRUD stays in the controller. |
| **DTO / Value Object** | Input is complex/reused enough that typed data beats `$request->validated()`. |
| **Domain Event + Listener** | A mutation has side effects another domain must react to (audit, webhooks). Reads never emit events. |
| **Strategy / Pipeline / Chain / etc.** | There is genuinely more than one algorithm or a multi-stage flow *today* — not hypothetically. |

Each epic's **🧱 Design Patterns** section lists *candidate* patterns for its stories with an explicit adoption trigger. Treat them as "use if/when needed," not as required scaffolding.

See the [main README](../../README.md) and [Complete API specification](../Complete_APIs.md) for request/response envelopes and full schemas.
