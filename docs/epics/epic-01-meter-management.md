# Epic 01 — Meter Management

## 🧩 Epic Overview

**Goal:** Provide reliable, auditable management of the meter fleet across its full lifecycle — onboarding, calibration, and retirement.

**Why (Business / User Value):**

Give field teams and meter administrators a reliable way to onboard, calibrate, and retire meters so the platform always reflects the true physical fleet. Accurate meter lifecycle data is the foundation for trustworthy readings, billing, and audits.

---

## 🚀 Features

### 📦 Feature 1: Meter Onboarding

**Description:** Register individual meters with metadata and bulk-import large batches from CSV.

### 📖 User Story 1: Register Meter

**As a** Field Operator, **I want to** register a meter with its metadata, **so that** the meter is added to the system and ready for readings.

`POST /api/v1/energy_conversion/register-meter`

**Acceptance Criteria:**

- [ ] A valid request creates a meter and returns a `SUCCESS` response with the new meter `id`.
- [ ] Requests without the `energy_conversion.write` scope are rejected with `403`.
- [ ] Missing or invalid `organizationId` / `requesterId` returns `400` with field-level messages.
- [ ] A repeated `Idempotency-Key` returns the original result instead of creating a duplicate.

**Tasks:**

- [ ] Define request validation for the meter metadata payload.
- [ ] Implement scope + organization permission checks.
- [ ] Persist the meter and emit a domain event + audit record.
- [ ] Wire up idempotency handling via the `Idempotency-Key` header.

---

### 📖 User Story 2: Bulk Import Meter Registrations

**As a** Field Operator, **I want to** bulk import meter registrations from a CSV, **so that** I can onboard many meters at once.

`POST /api/v1/energy_conversion/bulk-import-meter-registrations`

**Acceptance Criteria:**

- [ ] A valid CSV batch registers all rows and returns a per-row outcome summary.
- [ ] Invalid rows are reported with field-level errors without blocking valid rows.
- [ ] The whole operation is idempotent for a given `Idempotency-Key`.

**Tasks:**

- [ ] Parse and validate the CSV payload.
- [ ] Process rows and aggregate success/failure results.
- [ ] Emit domain events and audit records for the batch.

---

### 📦 Feature 2: Meter Lifecycle & Calibration

**Description:** Manage the ongoing state of a meter — apply calibration adjustments and deactivate meters that should no longer be read or billed.

### 📖 User Story 1: Upload Calibration Data

**As an** Engineer, **I want to** upload calibration data, **so that** meter calibration adjustments are applied to future readings.

`POST /api/v1/energy_conversion/upload-calibration-data`

**Acceptance Criteria:**

- [ ] Calibration data is validated and applied to the target meter.
- [ ] An audit record captures who applied the calibration and when.
- [ ] Invalid calibration values return `400` with field-level messages.

**Tasks:**

- [ ] Validate calibration payload against meter constraints.
- [ ] Persist calibration adjustments and emit a domain event.
- [ ] Record the change in the audit trail.

---

### 📖 User Story 2: Deactivate Meter

**As a** Meter Admin, **I want to** deactivate a meter, **so that** it stops accepting new readings and billings.

`POST /api/v1/energy_conversion/deactivate-meter`

**Acceptance Criteria:**

- [ ] A deactivated meter rejects new readings and is excluded from billing jobs.
- [ ] Deactivating an already-inactive meter returns a safe, idempotent result.
- [ ] The action is recorded in the meter's audit trail.

**Tasks:**

- [ ] Implement deactivation state transition with concurrency handling (`409`).
- [ ] Emit a domain event and write an audit record.
- [ ] Ensure downstream readings/billing respect the inactive state.

---

## 🧱 Design Patterns

> Core patterns (Action · Form Request · API Resource · DTO / Value Object · Domain Events + Listener · Idempotency Middleware · Audit Log) apply to **every** endpoint — see the [epics README](README.md#-design-patterns). The table below highlights the patterns most relevant to this epic.

| Pattern | Justification | Applied To |
| --- | --- | --- |
| **Action** | Encapsulates each use case as a single-responsibility class, keeping controllers thin | All endpoints |
| **State (State Machine)** | Meter status must change behavior and guard illegal/concurrent transitions | Deactivate Meter |
| **Pipeline** | CSV rows flow through discrete, ordered, testable stages | Bulk Import |
| **Batch / Queued Job** | Large imports run asynchronously without blocking the request | Bulk Import |
| **Observer** | Lifecycle changes recorded to the audit trail without coupling domain logic | Registration · Calibration · Deactivation |

### Pattern Details

**State (State Machine)**

- Intent: Let a meter change behavior as its lifecycle status changes.
- Problem it solves: Prevents illegal transitions (e.g. re-activating mid-billing) and lost updates under concurrency.
- Trade-offs:
    - ✅ Pro: Explicit, guarded transitions and clear valid states.
    - ⚠️ Con: More modeling overhead than a simple boolean flag.
- Where applied in this Epic: *Deactivate Meter* (`active → inactive`), returning `409` on conflicting transitions.

**Pipeline**

- Intent: Process input through a sequence of independent, ordered stages.
- Problem it solves: Keeps bulk CSV handling readable, with per-stage validation and isolation.
- Trade-offs:
    - ✅ Pro: Composable, individually testable stages.
    - ⚠️ Con: Indirection that is unnecessary for trivial flows.
- Where applied in this Epic: *Bulk Import* (parse → validate → persist → aggregate per-row results).

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Is this Production-ready?
