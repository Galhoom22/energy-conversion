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

The project uses a **domain-oriented modular monolith** (see the [main README](../../README.md#-architecture)). The following **core patterns apply to every endpoint** and are assumed by each epic — individual epics list only their *additional*, situational patterns.

| Pattern | Role |
| ------- | ---- |
| **Action** | One class per use case (e.g. `RegisterMeter`); holds the business logic and keeps controllers thin. |
| **Form Request** | Validation (`rules()`) + authorization (`authorize()` checks the Sanctum ability). |
| **API Resource** | Shapes the standard response envelope (`id`, `status`, `processedAt`, `details`). |
| **DTO / Value Object** | Carries validated input into Actions as typed data instead of raw arrays. |
| **Domain Events + Listener** | Each successful mutation emits an event; listeners handle side effects (audit, webhooks) and decouple domains. |
| **Idempotency Middleware** | Wraps mutating requests to honor the `Idempotency-Key` header. |
| **Audit Log** | Append-only audit records written by listeners across all domains. |

Situational patterns (Strategy, Pipeline, State, Specification, Pub/Sub, Batch Jobs, etc.) are documented in each epic's own **🧱 Design Patterns** section.

See the [main README](../../README.md) and [Complete API specification](../Complete_APIs.md) for request/response envelopes and full schemas.
