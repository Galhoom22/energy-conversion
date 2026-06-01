# Epic 05 — Reporting, Audit & Integration

## 🧩 Epic Overview

**Goal:** Satisfy compliance and integration needs through regulatory reporting, auditability, and outbound webhooks.

**Why (Business / User Value):**

Meet regulatory obligations, prove the integrity of meter records, and let external systems react to platform events in real time. This epic covers compliance reporting, auditability, and outbound integration.

---

## 🚀 Features

### 📦 Feature 1: Regulatory Reporting

**Description:** Produce the files regulators require from platform data.

### 📖 User Story 1: Export Regulatory Report

**As a** Regulatory user, **I want to** export a regulatory report, **so that** I can produce regulator-required files.

`POST /api/v1/energy_conversion/export-regulatory-report`

**Acceptance Criteria:**

- [ ] Produces a report file in the required regulatory format.
- [ ] Export is scoped to the requester's organization and authorized scope.
- [ ] The export action is recorded in the audit trail.

**Tasks:**

- [ ] Implement report generation in the required format.
- [ ] Enforce scope and organization checks.
- [ ] Emit a domain event and write an audit record.

---

### 📦 Feature 2: Audit Trail

**Description:** Expose the history of changes to meter records for review.

### 📖 User Story 1: Get Audit Trail For Meter

**As an** Auditor, **I want to** get the audit trail for a meter, **so that** I can review changes to its records.

`POST /api/v1/energy_conversion/get-audit-trail-for-meter`

**Acceptance Criteria:**

- [ ] Returns a chronological list of changes for the given meter.
- [ ] Access is restricted by scope and organization.
- [ ] Requests for unknown meters return a clear error.

**Tasks:**

- [ ] Implement audit trail retrieval endpoint.
- [ ] Enforce authorization checks.
- [ ] Shape the response for audit review workflows.

---

### 📦 Feature 3: Outbound Integration

**Description:** Let external systems subscribe to platform events such as invoice creation.

### 📖 User Story 1: Subscribe Webhooks For Invoices

**As an** Integration partner, **I want to** subscribe to invoice webhooks, **so that** I receive `invoice.created` events.

`POST /api/v1/energy_conversion/subscribe-webhooks-for-invoices`

**Acceptance Criteria:**

- [ ] A valid subscription registers a webhook endpoint for `invoice.created` events.
- [ ] Duplicate subscriptions are handled idempotently.
- [ ] The subscription is recorded in the audit trail.

**Tasks:**

- [ ] Validate and persist the webhook subscription.
- [ ] Deliver `invoice.created` events to subscribed endpoints.
- [ ] Emit a domain event and write an audit record.

---

## 🧱 Design Patterns

> Core patterns (Action · Form Request · API Resource · DTO / Value Object · Domain Events + Listener · Idempotency Middleware · Audit Log) apply to **every** endpoint — see the [epics README](README.md#-design-patterns). The table below highlights the patterns most relevant to this epic.

| Pattern | Justification | Applied To |
| --- | --- | --- |
| **Action** | Encapsulates each use case as a single-responsibility class, keeping controllers thin | All endpoints |
| **Publish / Subscribe** | Deliver platform events to external subscribers without coupling | Subscribe Webhooks |
| **Audit Log (append-only)** | Immutable, queryable change history | Get Audit Trail |
| **Adapter** | Map internal data to required regulatory formats | Export Regulatory Report |
| **Builder** | Assemble report documents section by section | Export Regulatory Report |
| **Batch / Queued Job** | Async report generation & webhook delivery with retries | Export Report · Subscribe Webhooks |

### Pattern Details

**Publish / Subscribe**

- Intent: Broadcast events to interested subscribers without the publisher knowing who consumes them.
- Problem it solves: External systems need to react to `invoice.created` without the platform being coupled to them.
- Trade-offs:
    - ✅ Pro: Decoupled, extensible integration; new subscribers add no core changes.
    - ⚠️ Con: Delivery, retry, and observability complexity (at-least-once, ordering).
- Where applied in this Epic: *Subscribe Webhooks For Invoices*.

**Adapter**

- Intent: Convert one interface or data format into another the client expects.
- Problem it solves: Regulators require specific external file formats that differ from internal models.
- Trade-offs:
    - ✅ Pro: Isolates format concerns; internal models stay clean.
    - ⚠️ Con: An extra mapping layer to maintain per format.
- Where applied in this Epic: *Export Regulatory Report*.

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Is this Production-ready?
