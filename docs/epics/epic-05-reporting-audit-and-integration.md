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

> **Convention-first.** Default to plain Laravel (Controller → Form Request → Eloquent → API Resource) as described in the [epics README](README.md#-design-patterns). The patterns below are **candidates** — adopt one only when the listed trigger is actually true, not preemptively.

| Candidate pattern | Adopt only when… | Relevant to |
| --- | --- | --- |
| **Events + Queued delivery** | Webhook delivery needs decoupling and retries (use Laravel events/jobs, not a custom bus) | Subscribe Webhooks |
| **Adapter** | A regulatory format differs enough from internal models to warrant isolating the mapping | Export Regulatory Report |
| **Batch / Queued Job** | Report generation or webhook delivery is slow enough to need async + retries | Export Report · Subscribe Webhooks |
| **Dedicated Action** | Report/subscription logic outgrows a controller method | Export Report · Subscribe Webhooks |

### Default vs. when-to-escalate

- **Get Audit Trail For Meter** — a read over existing audit records: thin controller + scoped Eloquent query + API Resource. The audit log itself is written by listeners on mutations (already a shared convention), not a new pattern here.
- **Subscribe Webhooks For Invoices** — persist the subscription with a Form Request; deliver `invoice.created` via a Laravel event + queued listener. Add retry/backoff only as delivery reliability needs grow.
- **Export Regulatory Report** — build the file with framework helpers first; introduce an Adapter per format **only when** a real format mismatch justifies it.

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Is this Production-ready?
