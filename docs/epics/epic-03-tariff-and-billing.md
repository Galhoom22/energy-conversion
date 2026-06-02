# Epic 03 — Tariff & Billing

## 🧩 Epic Overview

**Goal:** Turn raw consumption into accurate, defensible revenue through tariffs, simulation, invoicing, and exports.

**Why (Business / User Value):**

Let billing teams define pricing, model the impact of tariff changes before committing, generate invoices reliably, and export billing outputs. This turns raw consumption into accurate, defensible revenue.

---

## 🚀 Features

### 📦 Feature 1: Tariff Definition & Simulation

**Description:** Define time-of-use pricing rules and estimate their impact on meters and scenarios before they go live.

### 📖 User Story 1: Create Tariff

**As a** Billing Admin, **I want to** create a tariff, **so that** I can define time-of-use pricing rules.

`POST /api/v1/energy_conversion/create-tariff`

**Acceptance Criteria:**

- [ ] A valid tariff is created and returns a `SUCCESS` response with its `id`.
- [ ] Overlapping or contradictory pricing rules return `400` with field-level messages.
- [ ] Tariff creation is idempotent for a given `Idempotency-Key`.

**Tasks:**

- [ ] Validate tariff structure and time-of-use windows.
- [ ] Persist the tariff and emit a domain event + audit record.
- [ ] Implement idempotency handling.

---

### 📖 User Story 2: Simulate Tariff On Meter

**As a** Billing Admin, **I want to** simulate a tariff on a meter, **so that** I can estimate bills under tariff changes.

`POST /api/v1/energy_conversion/simulate-tariff-on-meter`

**Acceptance Criteria:**

- [ ] Returns an estimated bill for the meter under the given tariff.
- [ ] Simulation does not mutate live billing data.

**Tasks:**

- [ ] Implement read-only simulation against historical consumption.
- [ ] Shape the estimate response payload.

---

### 📖 User Story 3: Manage Tariff Simulations

**As an** Admin, **I want to** batch-run tariff simulations, **so that** I can compare multiple scenarios.

`POST /api/v1/energy_conversion/manage-tariff-simulations`

**Acceptance Criteria:**

- [ ] Multiple scenarios can be queued and run as a batch.
- [ ] Each scenario result is returned with a clear identifier.

**Tasks:**

- [ ] Implement batch simulation orchestration.
- [ ] Aggregate and return per-scenario results.

---

### 📦 Feature 2: Invoice Generation & Export

**Description:** Generate invoices for a billing period and archive billing outputs as CSV.

### 📖 User Story 1: Run Billing Job

**As a** Billing System, **I want to** run a billing job, **so that** invoices are generated for a period.

`POST /api/v1/energy_conversion/run-billing-job`

**Acceptance Criteria:**

- [ ] A valid job generates invoices for the requested period.
- [ ] Inactive meters are excluded from billing.
- [ ] Re-running the same job with an `Idempotency-Key` does not double-bill.

**Tasks:**

- [ ] Implement billing job orchestration over eligible meters.
- [ ] Generate invoices and emit `invoice.created` domain events.
- [ ] Write audit records and ensure idempotent execution.

---

### 📖 User Story 2: Export Billing CSV

**As an** Operator, **I want to** export billing as CSV, **so that** I can archive billing outputs.

`POST /api/v1/energy_conversion/export-billing-CSV`

**Acceptance Criteria:**

- [ ] Produces a CSV export of billing outputs for the requested scope.
- [ ] Export access is restricted by scope and organization.

**Tasks:**

- [ ] Implement CSV export generation.
- [ ] Enforce authorization and write an audit record.

---

## 🧱 Design Patterns

> **Convention-first.** Default to plain Laravel (Controller → Form Request → Eloquent → API Resource) as described in the [epics README](README.md#-design-patterns). The patterns below are **candidates** — adopt one only when the listed trigger is actually true, not preemptively. This is the most logic-heavy epic, so escalation is more likely here than elsewhere — but still demand a concrete justification.

| Candidate pattern | Adopt only when… | Relevant to |
| --- | --- | --- |
| **Dedicated Action** | Billing/tariff logic clearly outgrows a controller method | Create Tariff · Run Billing Job |
| **Strategy** | More than one rate-resolution rule actually exists | Create Tariff · Run Billing Job |
| **Pipeline** | Billing genuinely needs multiple ordered, testable stages | Run Billing Job |
| **Batch / Queued Job** | Billing/simulation runs are long enough to need async execution | Run Billing Job · Manage Tariff Simulations |
| **Builder** | Invoice/CSV assembly is complex enough that inline building gets unclear | Run Billing Job · Export Billing CSV |

### Default vs. when-to-escalate

- **Create Tariff** — start with a Form Request + Eloquent write; add a Strategy for rate resolution **only when** a second rule type appears.
- **Simulate Tariff On Meter** — a read-only calculation; keep it a focused service/method that never mutates billing state.
- **Run Billing Job** — likely warrants a dedicated Action and a queued job given its orchestration; introduce a Pipeline only if stages multiply.
- **Export Billing CSV** — use Laravel's CSV/streamed-response helpers before any Builder abstraction.

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Is this Production-ready?
