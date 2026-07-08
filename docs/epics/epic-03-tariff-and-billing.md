# Epic 03 — Tariff & Billing

## 🔍 Overview

| Field | Value |
| --- | --- |
| **Goal** | Turn raw consumption into accurate, defensible revenue through tariffs, simulation, invoicing, and exports. |
| **Business value** | Billing teams can price consumption, preview the impact of tariff changes before committing, and generate invoices reliably. |
| **User value** | Billing admins define pricing and model scenarios safely; the billing system generates invoices for a period; operators archive outputs. |
| **Success metric** | Billing runs are idempotent (no double-billing); inactive meters are excluded; simulations never mutate live billing data. |
| **Out of scope** | Meter lifecycle (Epic 01), reading capture/correction (Epic 02), reconciliation & settlement (Epic 04), reporting & webhooks (Epic 05). |

---

## 🧩 Features

### Feature 1: Tariff Definition & Simulation

> **Description** — Define time-of-use pricing rules and estimate their impact on meters and scenarios before they go live.

### User Story 1: Create Tariff

> As a **Billing Admin**, I want to create a tariff, so that I can define time-of-use pricing rules.

`POST /api/v1/energy_conversion/create-tariff`

**Acceptance Criteria**

- [ ] A valid tariff is created and returns a `SUCCESS` response with its `id`.
- [ ] Overlapping or contradictory pricing rules return `400` with field-level messages.
- [ ] Tariff creation is idempotent for a given `Idempotency-Key`.

**Tasks**

- [ ] Validate tariff structure and time-of-use windows.
- [ ] Persist the tariff and emit a domain event + audit record.
- [ ] Implement idempotency handling.

---

### User Story 2: Simulate Tariff On Meter

> As a **Billing Admin**, I want to simulate a tariff on a meter, so that I can estimate bills under tariff changes.

`POST /api/v1/energy_conversion/simulate-tariff-on-meter`

**Acceptance Criteria**

- [ ] Returns an estimated bill for the meter under the given tariff.
- [ ] Simulation does not mutate live billing data.

**Tasks**

- [ ] Implement read-only simulation against historical consumption.
- [ ] Shape the estimate response payload.

---

### User Story 3: Manage Tariff Simulations

> As an **Admin**, I want to batch-run tariff simulations, so that I can compare multiple scenarios.

`POST /api/v1/energy_conversion/manage-tariff-simulations`

**Acceptance Criteria**

- [ ] Multiple scenarios can be queued and run as a batch.
- [ ] Each scenario result is returned with a clear identifier.

**Tasks**

- [ ] Implement batch simulation orchestration.
- [ ] Aggregate and return per-scenario results.

---

### Feature 2: Invoice Generation & Export

> **Description** — Generate invoices for a billing period and archive billing outputs as CSV.

### User Story 1: Run Billing Job

> As a **Billing System**, I want to run a billing job, so that invoices are generated for a period.

`POST /api/v1/energy_conversion/run-billing-job`

**Acceptance Criteria**

- [ ] A valid job generates invoices for the requested period.
- [ ] Inactive meters are excluded from billing.
- [ ] Re-running the same job with an `Idempotency-Key` does not double-bill.

**Tasks**

- [ ] Implement billing job orchestration over eligible meters.
- [ ] Generate invoices and emit `invoice.created` domain events.
- [ ] Write audit records and ensure idempotent execution.

---

### User Story 2: Export Billing CSV

> As an **Operator**, I want to export billing as CSV, so that I can archive billing outputs.

`POST /api/v1/energy_conversion/export-billing-CSV`

**Acceptance Criteria**

- [ ] Produces a CSV export of billing outputs for the requested scope.
- [ ] Export access is restricted by scope and organization.

**Tasks**

- [ ] Implement CSV export generation.
- [ ] Enforce authorization and write an audit record.

---

## 🏗️ Design Patterns

> **Convention-first**, but this is the most logic-heavy epic, so escalation is more likely here than elsewhere — still demand a concrete justification before adopting anything below.

| Pattern | Justification | Applied To |
| --- | --- | --- |
| Event + Listener | Mutations write audit records and `run-billing-job` emits `invoice.created` for Epic 05 webhooks. | Create Tariff · Run Billing Job · Export CSV |
| Idempotency middleware | Billing must never double-bill on a replayed key. | Create Tariff · Run Billing Job |
| Dedicated Action | Run Billing Job has real orchestration (select eligible meters → price → invoice → emit) that does not belong in a controller. | Run Billing Job |

### Deferred (do **not** build until the trigger is true)

| Pattern | Adopt only when… | Candidate for |
| --- | --- | --- |
| Strategy | A second rate-resolution rule genuinely exists. | Create Tariff · Run Billing Job |
| Pipeline | Billing genuinely needs multiple ordered, independently-tested stages. | Run Billing Job |
| Queued Batch job | Billing/simulation runs are long enough to need async execution. | Run Billing Job · Manage Tariff Simulations |
| Builder | Invoice/CSV assembly gets unclear when built inline (use Laravel streamed-CSV helpers first). | Run Billing Job · Export CSV |

---

## 📦 Required Packages

Uses **`brick/money`** for exact tariff/invoice arithmetic (no float rounding — a correctness requirement) and **`league/csv`** for streaming Billing CSV export. See the [canonical Packages list](README.md#-packages) for compatibility, install commands, and rejected alternatives.

---

## 🧪 Testing

> **Goal:** every acceptance criterion is backed by a Pest feature test, plus the edge cases below.

### Feature tests (Pest) — one per acceptance criterion

- [ ] Create Tariff: valid tariff created; overlapping/contradictory rules → `400`; replayed key → idempotent.
- [ ] Simulate Tariff: returns estimate; asserts no billing data was mutated.
- [ ] Manage Simulations: multiple scenarios run; each result returned with its identifier.
- [ ] Run Billing Job: invoices generated for period; inactive meters excluded; replayed key → no double-billing.
- [ ] Export CSV: produces CSV for scope; access restricted by scope/org.

### Edge cases that must have a test

| Category | Edge case | Covered |
| --- | --- | --- |
| Auth | Unauthenticated request → `401` | [ ] |
| Auth | Missing `energy_conversion.write` scope → `403` | [ ] |
| Tenancy | Billing/export across organizations is rejected | [ ] |
| Input | Invalid tariff windows / required fields → `400` | [ ] |
| State | Inactive meter excluded from a billing run | [ ] |
| State | Duplicate `Idempotency-Key` does not double-bill | [ ] |

---

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled gracefully?
- [ ] Is this production-ready (auth, tenancy, idempotency, audit all enforced)?
- [ ] Does every acceptance criterion have a passing Pest test?
- [ ] Are the edge cases above covered?
