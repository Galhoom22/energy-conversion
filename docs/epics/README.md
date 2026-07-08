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

## 📦 Packages

> **Convention-first still applies.** These are the *only* third-party packages the epics rely on; each solves a real, documented problem that plain Laravel doesn't cover well. Everything else is written natively. Per-epic docs reference this single list instead of repeating it, and dependencies are only installed when the owning epic is actually built.

### Package Evaluation Checklist (applied to every package below)

- [x] Solves a real, documented problem in an epic.
- [x] Not already solvable with native code or an installed package.
- [x] Size / dependency tree reviewed and minimal.
- [x] Actively maintained, Laravel 13 / PHP 8.4 compatible, no known vulnerabilities.
- [x] Active maintainer / community.

| Package | Problem It Solves | Why Not Native | Install | Used By | Official |
| --- | --- | --- | --- | --- | --- |
| `brick/money` | Exact money & currency arithmetic (no float rounding) | Float math silently mis-rounds currency — a real financial/correctness bug | `composer require brick/money:^0.13` | Epic 03 · Epic 04 | [github.com/brick/money](https://github.com/brick/money) |
| `league/csv` | Memory-efficient CSV read/write (streaming, BOM, encoding, quoting) | `fgetcsv`/`fputcsv` need hand-rolled handling for large / edge-case files | `composer require league/csv:^9.28` | Epic 01 · Epic 03 · Epic 04 | [csv.thephpleague.com](https://csv.thephpleague.com/) |
| `spatie/laravel-webhook-server` | Signed, queued, retryable outbound webhooks (backoff + replay protection) | Raw `Http::post` has no signing / retry / backoff guarantees | `composer require spatie/laravel-webhook-server:^3.10` | Epic 05 | [github.com/spatie/laravel-webhook-server](https://github.com/spatie/laravel-webhook-server) |

### Compatibility (verified against Packagist)

| Package | Requires | Project (PHP 8.4 · Laravel 13.8) |
| --- | --- | --- |
| `brick/money ^0.13` | `php ^8.2`, `brick/math`, `psr/simple-cache` (framework-agnostic) | ✅ — pin to `0.13.*` (0.x release line) |
| `league/csv ^9.28` | `php ^8.1.2`, `ext-filter` (framework-agnostic) | ✅ |
| `spatie/laravel-webhook-server ^3.10` | `illuminate/* …^13.0`, `guzzle ^7`, `spatie/laravel-package-tools ^1.11` | ✅ |

### 🚫 Rejected / Deferred (documented so they aren't re-proposed)

| Package | Decision | Reason |
| --- | --- | --- |
| `owen-it/laravel-auditing` | **Deferred** | Compatible and a genuine time-saver, but (1) it contradicts the documented **Event + Listener → audit** convention, and (2) much of our audit is *action-level* (billing ran, report exported, manual match) rather than the model-attribute changes it captures automatically. Revisit only if audit becomes purely model-CRUD and we adopt it as the single audit mechanism. |
| `maatwebsite/excel` | Deferred | Heavier (PhpSpreadsheet). Only if `.xlsx` is genuinely required; `league/csv` covers CSV. |
| `barryvdh/laravel-dompdf` | Deferred | Only if a regulatory report must be PDF; the required format isn't confirmed yet (Epic 05). |
| `devhammed/laravel-brick-money` | Rejected | Small / new integration; a ~15-line native Eloquent cast is enough. |
| `spatie/laravel-data` (DTO) | Deferred | YAGNI — Form Requests + API Resources cover the payloads. |
| Any idempotency package | Rejected | Native `Idempotency-Key` middleware is the documented approach. |
