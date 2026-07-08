# Epic 04 — Reconciliation & Settlement

## 🔍 Overview

| Field | Value |
| --- | --- |
| **Goal** | Close the financial loop by matching payments to invoices and settling totals per participant. |
| **Business value** | Accurate reconciliation keeps the books correct and gives finance confidence in revenue figures. |
| **User value** | Finance can import statements and auto-match payments; operators can fix unmatched entries manually; settlement admins get per-participant totals. |
| **Success metric** | Imports and settlement runs are idempotent; unmatched entries are surfaced for manual handling; every match is audited. |
| **Out of scope** | Meter lifecycle (Epic 01), readings (Epic 02), tariff/invoice generation (Epic 03), reporting & webhooks (Epic 05). |

---

## 🧩 Features

### Feature 1: Payment Reconciliation

> **Description** — Import bank statements to auto-match payments to invoices, and manually link payments when automated heuristics fall short.

### User Story 1: Import Bank Statement For Reconciliation

> As a **Finance user**, I want to import a bank statement, so that payments are matched to invoices.

`POST /api/v1/energy_conversion/import-bank-statement-for-reconciliation`

**Acceptance Criteria**

- [ ] A valid statement is imported and auto-matched against open invoices.
- [ ] Unmatched entries are clearly surfaced for manual handling.
- [ ] Re-importing the same statement with an `Idempotency-Key` does not duplicate matches.

**Tasks**

- [ ] Parse and validate the bank statement payload.
- [ ] Implement auto-matching against invoices.
- [ ] Emit domain events and write audit records.

---

### User Story 2: Manual Reconciliation Match

> As an **Operator**, I want to manually match a payment to an invoice, so that reconciliation completes when heuristics fail.

`POST /api/v1/energy_conversion/manual-reconciliation-match`

**Acceptance Criteria**

- [ ] An operator can link a specific payment to a specific invoice.
- [ ] Conflicting or already-matched links are handled with `409`.
- [ ] The manual match is recorded in the audit trail.

**Tasks**

- [ ] Implement manual match endpoint with conflict handling.
- [ ] Emit a domain event and write an audit record.

---

### Feature 2: Settlement

> **Description** — Calculate settlement totals per participant for a period.

### User Story 1: Generate Settlement Summary

> As a **Settlement Admin**, I want to generate a settlement summary, so that totals per participant are calculated.

`POST /api/v1/energy_conversion/generate-settlement-summary`

**Acceptance Criteria**

- [ ] Produces per-participant totals for the requested period.
- [ ] Summary generation is idempotent for a given `Idempotency-Key`.
- [ ] The generated summary is recorded in the audit trail.

**Tasks**

- [ ] Implement settlement calculation logic.
- [ ] Aggregate totals per participant and shape the response.
- [ ] Emit a domain event and write an audit record.

---

## 🏗️ Design Patterns

> **Convention-first.** Default to plain Laravel (Controller → Form Request → Eloquent → API Resource).

| Pattern | Justification | Applied To |
| --- | --- | --- |
| Event + Listener | Each match/settlement must write an audit record without coupling the controller to that side effect. | All three endpoints |
| Idempotency middleware | Re-imported statements and re-run summaries must not duplicate matches/totals. | Import Statement · Generate Settlement |

### Deferred (do **not** build until the trigger is true)

| Pattern | Adopt only when… | Candidate for |
| --- | --- | --- |
| Strategy | More than one matching heuristic (reference, amount, fuzzy) actually exists. | Import Bank Statement |
| Chain of Responsibility | Multiple heuristics must run in sequence with fall-through. | Import → Manual Match |
| Queued Batch job | Statements are large enough to need async processing. | Import Bank Statement |
| Dedicated Action | Matching/settlement logic outgrows a controller method. | Import · Manual Match · Settlement |

> **Manual Match** and **Generate Settlement** start as a guarded Eloquent update / grouped aggregate query — no special pattern needed.

---

## 📦 Required Packages

Uses **`league/csv`** to parse imported bank-statement files and **`brick/money`** for exact amount matching & settlement totals — both shared with Epic 03, so they're installed once. Manual match and settlement stay plain guarded Eloquent / aggregate queries. See the [canonical Packages list](README.md#-packages).

---

## 🧪 Testing

> **Goal:** every acceptance criterion is backed by a Pest feature test, plus the edge cases below.

### Feature tests (Pest) — one per acceptance criterion

- [ ] Import Statement: valid statement auto-matched; unmatched entries surfaced; replayed key → no duplicate matches.
- [ ] Manual Match: payment linked to invoice; conflicting/already-matched → `409`; match audited.
- [ ] Generate Settlement: per-participant totals for period; replayed key → idempotent; summary audited.

### Edge cases that must have a test

| Category | Edge case | Covered |
| --- | --- | --- |
| Auth | Unauthenticated request → `401` | [ ] |
| Auth | Missing `energy_conversion.write` scope → `403` | [ ] |
| Tenancy | Matching/settling across organizations is rejected | [ ] |
| Input | Malformed statement / required fields → `400` | [ ] |
| State | Duplicate `Idempotency-Key` does not duplicate matches/totals | [ ] |
| State | Conflicting/already-matched link → `409` | [ ] |

---

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled gracefully?
- [ ] Is this production-ready (auth, tenancy, idempotency, audit all enforced)?
- [ ] Does every acceptance criterion have a passing Pest test?
- [ ] Are the edge cases above covered?
