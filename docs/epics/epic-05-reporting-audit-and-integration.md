# Epic 05 — Reporting, Audit & Integration

## 🧩 Epic Overview

**Goal: Why (Business / User Value):**

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

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Are scope/authorization checks enforced (`401` / `403`)?
- [ ] Is idempotency honored for mutating operations?
- [ ] Are domain events and audit records emitted?
- [ ] Is this Production-ready?
