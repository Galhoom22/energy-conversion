# Epic 05 — Reporting, Audit & Integration

## 🔍 Overview

| Field | Value |
| --- | --- |
| **Goal** | Satisfy compliance and integration needs through regulatory reporting, auditability, and outbound webhooks. |
| **Business value** | Meet regulatory obligations, prove the integrity of meter records, and let external systems react to platform events. |
| **User value** | Regulatory users export required files; auditors review the history of a meter; integration partners receive `invoice.created` events. |
| **Success metric** | Reports are scoped & audited; audit trails are complete and queryable; subscribed endpoints reliably receive `invoice.created`. |
| **Out of scope** | Meter lifecycle (Epic 01), readings (Epic 02), tariff/billing generation (Epic 03), reconciliation (Epic 04). |

---

## 🧩 Features

### Feature 1: Regulatory Reporting

> **Description** — Produce the files regulators require from platform data.

### User Story 1: Export Regulatory Report

> As a **Regulatory user**, I want to export a regulatory report, so that I can produce regulator-required files.

`POST /api/v1/energy_conversion/export-regulatory-report`

**Acceptance Criteria**

- [ ] Produces a report file in the required regulatory format.
- [ ] Export is scoped to the requester's organization and authorized scope.
- [ ] The export action is recorded in the audit trail.

**Tasks**

- [ ] Implement report generation in the required format.
- [ ] Enforce scope and organization checks.
- [ ] Emit a domain event and write an audit record.

---

### Feature 2: Audit Trail

> **Description** — Expose the history of changes to meter records for review.

### User Story 1: Get Audit Trail For Meter

> As an **Auditor**, I want to get the audit trail for a meter, so that I can review changes to its records.

`POST /api/v1/energy_conversion/get-audit-trail-for-meter`

**Acceptance Criteria**

- [ ] Returns a chronological list of changes for the given meter.
- [ ] Access is restricted by scope and organization.
- [ ] Requests for unknown meters return a clear error.

**Tasks**

- [ ] Implement audit trail retrieval endpoint.
- [ ] Enforce authorization checks.
- [ ] Shape the response for audit review workflows.

---

### Feature 3: Outbound Integration

> **Description** — Let external systems subscribe to platform events such as invoice creation.

### User Story 1: Subscribe Webhooks For Invoices

> As an **Integration partner**, I want to subscribe to invoice webhooks, so that I receive `invoice.created` events.

`POST /api/v1/energy_conversion/subscribe-webhooks-for-invoices`

**Acceptance Criteria**

- [ ] A valid subscription registers a webhook endpoint for `invoice.created` events.
- [ ] Duplicate subscriptions are handled idempotently.
- [ ] The subscription is recorded in the audit trail.

**Tasks**

- [ ] Validate and persist the webhook subscription.
- [ ] Deliver `invoice.created` events to subscribed endpoints.
- [ ] Emit a domain event and write an audit record.

---

## 🏗️ Design Patterns

> **Convention-first.** Default to plain Laravel (Controller → Form Request → Eloquent → API Resource). The audit log itself is written by listeners on mutations (a shared convention from earlier epics), not a new pattern here.

| Pattern | Justification | Applied To |
| --- | --- | --- |
| Event + Queued Listener | Webhook delivery must be decoupled and retryable; use Laravel events/queued listeners, not a custom bus. | Subscribe Webhooks (delivers `invoice.created`) |
| Idempotency middleware | Duplicate subscription requests must be safe. | Subscribe Webhooks |

### Deferred (do **not** build until the trigger is true)

| Pattern | Adopt only when… | Candidate for |
| --- | --- | --- |
| Adapter | A regulatory format differs enough from internal models to warrant isolating the mapping. | Export Regulatory Report |
| Queued Batch job | Report generation is slow enough to need async + retries. | Export Regulatory Report |
| Dedicated Action | Report/subscription logic outgrows a controller method. | Export Report · Subscribe Webhooks |

> **Get Audit Trail** is a read: thin controller + scoped Eloquent query + API Resource — no event, Action, or DTO.

---

## 🧪 Testing

> **Goal:** every acceptance criterion is backed by a Pest feature test, plus the edge cases below.

### Feature tests (Pest) — one per acceptance criterion

- [ ] Export Report: produces file in required format; scoped to org + scope; export audited.
- [ ] Get Audit Trail: chronological changes for a meter; access restricted by scope/org; unknown meter → clear error.
- [ ] Subscribe Webhooks: valid subscription registered; duplicate → idempotent; subscription audited; `invoice.created` is delivered to the endpoint.

### Edge cases that must have a test

| Category | Edge case | Covered |
| --- | --- | --- |
| Auth | Unauthenticated request → `401` | [ ] |
| Auth | Missing `energy_conversion.write` scope → `403` | [ ] |
| Tenancy | Reading audit/exports across organizations is rejected | [ ] |
| Input | Invalid report params / webhook URL → `400` | [ ] |
| State | Duplicate subscription handled idempotently | [ ] |
| Integration | `invoice.created` delivery retried on failure | [ ] |

---

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled gracefully?
- [ ] Is this production-ready (auth, tenancy, idempotency, audit all enforced)?
- [ ] Does every acceptance criterion have a passing Pest test?
- [ ] Are the edge cases above covered?
