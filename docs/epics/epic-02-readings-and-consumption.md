# Epic 02 — Readings & Consumption

## 🔍 Overview

| Field | Value |
| --- | --- |
| **Goal** | Capture, correct, and surface consumption data so it is accurate, auditable, and easy to query. |
| **Business value** | Reliable readings power billing, anomaly detection, and customer support — so ingestion must be robust and corrections traceable. |
| **User value** | Metering systems can store readings safely, operators can fix mistakes with an audit reason, and support/ops can query consumption and anomalies. |
| **Success metric** | Every reading mutation is audited; corrections preserve the original value + reason; consumption queries are always scoped to the requester's organization. |
| **Out of scope** | Meter lifecycle (Epic 01), tariffs & billing (Epic 03), reconciliation (Epic 04), reporting & webhooks (Epic 05). |

---

## 🧩 Features

### Feature 1: Reading Ingestion & Correction

> **Description** — Store batches of consumption readings from metering systems, and correct erroneous historical readings with an audit reason.

### User Story 1: Ingest Readings Batch

> As a **Metering System**, I want to ingest a batch of readings, so that consumption data is stored for billing and analysis.

`POST /api/v1/energy_conversion/ingest-readings-batch`

**Acceptance Criteria**

- [ ] A valid batch is stored and returns a `SUCCESS` response.
- [ ] Readings for inactive meters are rejected with a clear error.
- [ ] Re-submitting the same batch with an `Idempotency-Key` does not duplicate data.

**Tasks**

- [ ] Validate the batch payload and meter states.
- [ ] Persist readings and emit a domain event + audit record.
- [ ] Implement idempotent batch handling.

---

### User Story 2: Correct Reading

> As an **Operator**, I want to correct an erroneous historical reading with a reason, so that records are accurate and the change is auditable.

`POST /api/v1/energy_conversion/correct-reading`

**Acceptance Criteria**

- [ ] A correction requires a reason and updates the target reading.
- [ ] The original value and the correction reason are preserved in the audit trail.
- [ ] Concurrent corrections to the same reading are handled with `409`.

**Tasks**

- [ ] Validate the correction payload (target reading + reason).
- [ ] Apply the correction and write the audit record.
- [ ] Emit a domain event for downstream re-billing if needed.

---

### Feature 2: Consumption Insight

> **Description** — Query consumption, inspect raw telemetry, and detect anomalies for investigation.

### User Story 1: Query Customer Consumption

> As a **Customer Service agent**, I want to query a customer's consumption, so that I can answer billing inquiries.

`POST /api/v1/energy_conversion/query-customer-consumption`

**Acceptance Criteria**

- [ ] Returns consumption scoped to the requester's organization.
- [ ] Unauthorized cross-organization access is rejected with `403`.
- [ ] Invalid query parameters return `400` with field-level messages.

**Tasks**

- [ ] Implement consumption query with organization scoping.
- [ ] Enforce authorization checks.
- [ ] Shape the response payload for support workflows.

---

### User Story 2: Get Reading Telemetry

> As a **Support engineer**, I want to retrieve reading telemetry, so that I can diagnose meter behavior.

`POST /api/v1/energy_conversion/get-reading-telemetry`

**Acceptance Criteria**

- [ ] Returns detailed telemetry for a given meter/time range.
- [ ] Requests for unknown meters return a clear error.

**Tasks**

- [ ] Implement telemetry retrieval endpoint.
- [ ] Enforce scope and organization checks.

---

### User Story 3: Detect Negative Consumption

> As an **Ops team member**, I want to detect negative consumption, so that anomalies are flagged for investigation.

`POST /api/v1/energy_conversion/detect-negative-consumption`

**Acceptance Criteria**

- [ ] Negative or implausible consumption is flagged and returned.
- [ ] Each detection emits a domain event for follow-up.

**Tasks**

- [ ] Implement anomaly detection logic.
- [ ] Emit events and write audit records for flagged anomalies.

---

## 🏗️ Design Patterns

> **Convention-first.** Default to plain Laravel (Controller → Form Request → Eloquent → API Resource). Reads (Query Consumption, Get Telemetry) are a thin controller + scoped Eloquent query + Resource — no Action, DTO, or event.

| Pattern | Justification | Applied To |
| --- | --- | --- |
| Event + Listener | Mutations must write an audit record / notify downstream without coupling the controller to those side effects. | Ingest · Correct · Detect |
| Idempotency middleware | Replayed batches/corrections must not double-process. | Ingest · Correct |

### Deferred (do **not** build until the trigger is true)

| Pattern | Adopt only when… | Candidate for |
| --- | --- | --- |
| Queued Batch job | Reading batches are large enough that synchronous storage blocks the request. | Ingest Readings Batch |
| Specification / Strategy | More than one anomaly rule/threshold genuinely exists. | Detect Negative Consumption |
| Dedicated Action class | A write grows orchestration beyond a simple persist/update. | Ingest · Correct |

---

## 📦 Required Packages

**None — plain Laravel.** Ingestion, correction, queries, telemetry, and negative-consumption detection need no third-party package (validation → Eloquent → API Resource → events). See the [canonical Packages list](README.md#-packages).

---

## 🧪 Testing

> **Goal:** every acceptance criterion is backed by a Pest feature test, plus the edge cases below. Focus on real failure modes — not blanket coverage targets.

### Feature tests (Pest) — one per acceptance criterion

- [ ] Ingest: valid batch stored; inactive-meter reading rejected; replayed `Idempotency-Key` → no duplicates.
- [ ] Correct: reason required + target updated; original value + reason preserved in audit; concurrent correction → `409`.
- [ ] Query Consumption: scoped to org; cross-org access → `403`; invalid params → `400`.
- [ ] Telemetry: returns telemetry for meter/range; unknown meter → clear error.
- [ ] Detect: negative/implausible flagged; each detection emits an event.

### Edge cases that must have a test

| Category | Edge case | Covered |
| --- | --- | --- |
| Auth | Unauthenticated request → `401` | [ ] |
| Auth | Missing `energy_conversion.write` scope → `403` | [ ] |
| Tenancy | Querying another organization's data is rejected | [ ] |
| Input | Missing/invalid required fields → `400` with field messages | [ ] |
| State | Reading for an inactive meter is rejected | [ ] |
| State | Duplicate `Idempotency-Key` does not duplicate readings | [ ] |
| State | Concurrent correction → `409` | [ ] |

---

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled gracefully?
- [ ] Is this production-ready (auth, tenancy, idempotency, audit all enforced)?
- [ ] Does every acceptance criterion have a passing Pest test?
- [ ] Are the edge cases above covered?
