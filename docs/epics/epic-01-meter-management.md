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

> **Convention-first.** Default to plain Laravel (Controller → Form Request → Eloquent → API Resource) as described in the [epics README](README.md#-design-patterns). The patterns below are **candidates** — adopt one only when the listed trigger is actually true, not preemptively.

| Candidate pattern | Adopt only when… | Relevant to |
| --- | --- | --- |
| **State (State Machine)** | Meter status needs more than a boolean and must guard illegal/concurrent transitions | Deactivate Meter |
| **Pipeline** | Bulk import genuinely needs multiple ordered, independently-tested stages | Bulk Import |
| **Batch / Queued Job** | Imports are large enough that synchronous handling times out or blocks the request | Bulk Import |
| **Dedicated Action + Domain Event** | A write has real orchestration or side effects another domain must react to (audit) | Register · Calibrate · Deactivate |

### Default vs. when-to-escalate

- **Register Meter / Upload Calibration** — start as a thin controller + Form Request + Eloquent write + API Resource. Extract an Action only if logic grows beyond a simple persist.
- **Deactivate Meter** — a status column + a guarded update is enough; introduce an explicit State Machine **only if** transition rules multiply.
- **Bulk Import** — begin with a straightforward validated loop; escalate to a Pipeline and/or queued Batch job **only when** file sizes or stage complexity demand it.

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Is this Production-ready?
