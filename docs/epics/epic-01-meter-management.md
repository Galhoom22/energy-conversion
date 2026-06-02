# Epic 01 — Meter Management

## 🔍 Overview

| Field | Value |
| --- | --- |
| **Goal** | Reliable, auditable management of the meter fleet across its full lifecycle — onboarding, calibration, and retirement. |
| **Business value** | The platform always reflects the true physical fleet, which is the foundation for trustworthy readings, billing, and audits. |
| **User value** | Field teams and meter admins can onboard, calibrate, and retire meters with confidence and a clear audit trail. |
| **Success metric** | All four endpoints meet their acceptance criteria; every mutation produces an audit record; meter status is always accurate. |
| **Out of scope** | Reading ingestion/correction (Epic 02), tariffs & billing (Epic 03), reconciliation (Epic 04), reporting & webhooks (Epic 05). |

---

## 🧩 Features

### Feature 1: Meter Onboarding

> **Description** — Register individual meters with metadata, and bulk-import large batches from CSV.

### User Story 1: Register Meter

> As a **Field Operator**, I want to register a meter with its metadata, so that the meter is added to the system and ready for readings.

`POST /api/v1/energy_conversion/register-meter`

**Acceptance Criteria**

- [ ] A valid request creates a meter and returns a `SUCCESS` response with the new meter `id`.
- [ ] Requests without the `energy_conversion.write` scope are rejected with `403`.
- [ ] Missing or invalid `organizationId` / `requesterId` returns `400` with field-level messages.
- [ ] A repeated `Idempotency-Key` returns the original result instead of creating a duplicate.

**Tasks**

- [ ] Define request validation for the meter metadata payload.
- [ ] Implement scope + organization permission checks.
- [ ] Persist the meter and emit a domain event + audit record.
- [ ] Wire up idempotency handling via the `Idempotency-Key` header.

---

### User Story 2: Bulk Import Meter Registrations

> As a **Field Operator**, I want to bulk import meter registrations from a CSV, so that I can onboard many meters at once.

`POST /api/v1/energy_conversion/bulk-import-meter-registrations`

**Acceptance Criteria**

- [ ] A valid CSV batch registers all rows and returns a per-row outcome summary.
- [ ] Invalid rows are reported with field-level errors without blocking valid rows.
- [ ] The whole operation is idempotent for a given `Idempotency-Key`.

**Tasks**

- [ ] Parse and validate the CSV payload.
- [ ] Process rows and aggregate success/failure results.
- [ ] Emit domain events and audit records for the batch.

---

### Feature 2: Meter Lifecycle & Calibration

> **Description** — Manage the ongoing state of a meter: apply calibration adjustments and deactivate meters that should no longer be read or billed.

### User Story 1: Upload Calibration Data

> As an **Engineer**, I want to upload calibration data, so that meter calibration adjustments are applied to future readings.

`POST /api/v1/energy_conversion/upload-calibration-data`

**Acceptance Criteria**

- [ ] Calibration data is validated and applied to the target meter.
- [ ] An audit record captures who applied the calibration and when.
- [ ] Invalid calibration values return `400` with field-level messages.

**Tasks**

- [ ] Validate calibration payload against meter constraints.
- [ ] Persist calibration adjustments and emit a domain event.
- [ ] Record the change in the audit trail.

---

### User Story 2: Deactivate Meter

> As a **Meter Admin**, I want to deactivate a meter, so that it stops accepting new readings and billings.

`POST /api/v1/energy_conversion/deactivate-meter`

**Acceptance Criteria**

- [ ] A deactivated meter rejects new readings and is excluded from billing jobs.
- [ ] Deactivating an already-inactive meter returns a safe, idempotent result.
- [ ] The action is recorded in the meter's audit trail.

**Tasks**

- [ ] Implement the deactivation state transition with concurrency handling (`409`).
- [ ] Emit a domain event and write an audit record.
- [ ] Ensure downstream readings/billing respect the inactive state.

---

## 🏗️ Design Patterns

> **Convention-first.** Default to plain Laravel (Controller → Form Request → Eloquent → API Resource). Only the patterns actually applied in this epic are listed; everything else is deferred until its trigger is real.

| Pattern | Justification | Applied To |
| --- | --- | --- |
| Event + Listener | Each mutation must write an audit record (and let other domains react) without coupling the controller to those side effects. | All four endpoints |
| Idempotency middleware | Mutating endpoints must not double-process a replayed `Idempotency-Key`; one shared middleware covers them all. | All four endpoints |

### Deferred (do **not** build until the trigger is true)

| Pattern | Adopt only when… | Candidate for |
| --- | --- | --- |
| State machine | Meter status needs more than `active`/`inactive` and must guard multiple illegal transitions. | Deactivate Meter |
| Pipeline / Queued Batch job | CSV imports are large enough that synchronous handling times out or blocks the request. | Bulk Import |
| Dedicated Action class | A write grows real orchestration beyond a simple Eloquent persist/update. | Register · Calibrate · Deactivate |

---

## 🧪 Testing

> **Goal:** every acceptance criterion is backed by a Pest feature test, plus the edge cases below. Focus on real failure modes for this API — not blanket coverage targets.

### Feature tests (Pest) — one per acceptance criterion

- [ ] Register Meter: happy path, missing scope → `403`, invalid org/requester → `400`, duplicate `Idempotency-Key` → replayed result.
- [ ] Bulk Import: all-valid CSV → per-row success summary; mixed CSV → valid rows pass + invalid rows reported; replayed key → idempotent.
- [ ] Upload Calibration: valid calibration applied + audit record; invalid values → `400`.
- [ ] Deactivate Meter: active → inactive + audit; already-inactive → safe idempotent result; concurrent transition → `409`.

### Edge cases that must have a test

| Category | Edge case | Covered |
| --- | --- | --- |
| Auth | Unauthenticated request → `401` | [ ] |
| Auth | Missing `energy_conversion.write` scope → `403` | [ ] |
| Tenancy | Acting on a meter from another `organizationId` is rejected | [ ] |
| Input | Missing/invalid required fields → `400` with field messages | [ ] |
| State | Duplicate `Idempotency-Key` returns original result | [ ] |
| State | Concurrent deactivation → `409` | [ ] |

---

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled gracefully?
- [ ] Is this production-ready (auth, tenancy, idempotency, audit all enforced)?
- [ ] Does every acceptance criterion have a passing Pest test?
- [ ] Are the edge cases above covered?
