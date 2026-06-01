# Epic 04 — Reconciliation & Settlement

## 🧩 Epic Overview

**Goal: Why (Business / User Value):**

Close the financial loop by matching incoming payments to invoices and calculating settlement totals per participant. Accurate reconciliation keeps the books correct and gives finance confidence in revenue figures.

---

## 🚀 Features

### 📦 Feature 1: Payment Reconciliation

**Description:** Import bank statements to auto-match payments to invoices, and manually link payments when automated heuristics fall short.

### 📖 User Story 1: Import Bank Statement For Reconciliation

**As a** Finance user, **I want to** import a bank statement, **so that** payments are matched to invoices.

`POST /api/v1/energy_conversion/import-bank-statement-for-reconciliation`

**Acceptance Criteria:**

- [ ] A valid statement is imported and auto-matched against open invoices.
- [ ] Unmatched entries are clearly surfaced for manual handling.
- [ ] Re-importing the same statement with an `Idempotency-Key` does not duplicate matches.

**Tasks:**

- [ ] Parse and validate the bank statement payload.
- [ ] Implement auto-matching heuristics against invoices.
- [ ] Emit domain events and write audit records.

---

### 📖 User Story 2: Manual Reconciliation Match

**As an** Operator, **I want to** manually match a payment to an invoice, **so that** reconciliation completes when heuristics fail.

`POST /api/v1/energy_conversion/manual-reconciliation-match`

**Acceptance Criteria:**

- [ ] An operator can link a specific payment to a specific invoice.
- [ ] Conflicting or already-matched links are handled with `409`.
- [ ] The manual match is recorded in the audit trail.

**Tasks:**

- [ ] Implement manual match endpoint with conflict handling.
- [ ] Emit a domain event and write an audit record.

---

### 📦 Feature 2: Settlement

**Description:** Calculate settlement totals per participant for a period.

### 📖 User Story 1: Generate Settlement Summary

**As a** Settlement Admin, **I want to** generate a settlement summary, **so that** totals per participant are calculated.

`POST /api/v1/energy_conversion/generate-settlement-summary`

**Acceptance Criteria:**

- [ ] Produces per-participant totals for the requested period.
- [ ] Summary generation is idempotent for a given `Idempotency-Key`.
- [ ] The generated summary is recorded in the audit trail.

**Tasks:**

- [ ] Implement settlement calculation logic.
- [ ] Aggregate totals per participant and shape the response.
- [ ] Emit a domain event and write an audit record.

---

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Are scope/authorization checks enforced (`401` / `403`)?
- [ ] Is idempotency honored for mutating operations?
- [ ] Are domain events and audit records emitted?
- [ ] Is this Production-ready?
