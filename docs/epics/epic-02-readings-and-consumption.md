# Epic 02 — Readings & Consumption

## 🧩 Epic Overview

**Goal: Why (Business / User Value):**

Capture, correct, and surface consumption data accurately. Reliable readings power billing, anomaly detection, and customer support — so ingestion must be robust, corrections must be auditable, and consumption data must be easy to query.

---

## 🚀 Features

### 📦 Feature 1: Reading Ingestion & Correction

**Description:** Store batches of consumption readings from metering systems and correct erroneous historical readings with an audit reason.

### 📖 User Story 1: Ingest Readings Batch

**As a** Metering System, **I want to** ingest a batch of readings, **so that** consumption data is stored for billing and analysis.

`POST /api/v1/energy_conversion/ingest-readings-batch`

**Acceptance Criteria:**

- [ ] A valid batch is stored and returns a `SUCCESS` response.
- [ ] Readings for inactive meters are rejected with a clear error.
- [ ] Re-submitting the same batch with an `Idempotency-Key` does not duplicate data.

**Tasks:**

- [ ] Validate the batch payload and meter states.
- [ ] Persist readings and emit a domain event + audit record.
- [ ] Implement idempotent batch handling.

---

### 📖 User Story 2: Correct Reading

**As an** Operator, **I want to** correct an erroneous historical reading with a reason, **so that** records are accurate and the change is auditable.

`POST /api/v1/energy_conversion/correct-reading`

**Acceptance Criteria:**

- [ ] A correction requires a reason and updates the target reading.
- [ ] The original value and the correction reason are preserved in the audit trail.
- [ ] Concurrent corrections to the same reading are handled with `409`.

**Tasks:**

- [ ] Validate the correction payload (target reading + reason).
- [ ] Apply the correction and write the audit record.
- [ ] Emit a domain event for downstream re-billing if needed.

---

### 📦 Feature 2: Consumption Insight

**Description:** Query consumption, inspect raw telemetry, and detect anomalies for investigation.

### 📖 User Story 1: Query Customer Consumption

**As a** Customer Service agent, **I want to** query a customer's consumption, **so that** I can answer billing inquiries.

`POST /api/v1/energy_conversion/query-customer-consumption`

**Acceptance Criteria:**

- [ ] Returns consumption scoped to the requester's organization.
- [ ] Unauthorized cross-organization access is rejected with `403`.
- [ ] Invalid query parameters return `400` with field-level messages.

**Tasks:**

- [ ] Implement consumption query with organization scoping.
- [ ] Enforce authorization checks.
- [ ] Shape the response payload for support workflows.

---

### 📖 User Story 2: Get Reading Telemetry

**As a** Support engineer, **I want to** retrieve reading telemetry, **so that** I can diagnose meter behavior.

`POST /api/v1/energy_conversion/get-reading-telemetry`

**Acceptance Criteria:**

- [ ] Returns detailed telemetry for a given meter/time range.
- [ ] Requests for unknown meters return a clear error.

**Tasks:**

- [ ] Implement telemetry retrieval endpoint.
- [ ] Enforce scope and organization checks.

---

### 📖 User Story 3: Detect Negative Consumption

**As an** Ops team member, **I want to** detect negative consumption, **so that** anomalies are flagged for investigation.

`POST /api/v1/energy_conversion/detect-negative-consumption`

**Acceptance Criteria:**

- [ ] Negative or implausible consumption is flagged and returned.
- [ ] Each detection emits a domain event for follow-up.

**Tasks:**

- [ ] Implement anomaly detection logic.
- [ ] Emit events and write audit records for flagged anomalies.

---

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Are scope/authorization checks enforced (`401` / `403`)?
- [ ] Is idempotency honored for mutating operations?
- [ ] Are domain events and audit records emitted?
- [ ] Is this Production-ready?
