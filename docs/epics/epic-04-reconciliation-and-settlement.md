# Epic 04 — Reconciliation & Settlement

## 🧩 Epic Overview

**Goal:** Close the financial loop by matching payments to invoices and settling totals per participant.

**Why (Business / User Value):**

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

## 🧱 Design Patterns

> Core patterns (Action · Form Request · API Resource · DTO / Value Object · Domain Events + Listener · Idempotency Middleware · Audit Log) apply to **every** endpoint — see the [epics README](README.md#-design-patterns). The table below highlights the patterns most relevant to this epic.

| Pattern | Justification | Applied To |
| --- | --- | --- |
| **Action** | Encapsulates each use case as a single-responsibility class, keeping controllers thin | All endpoints |
| **Strategy** | Swappable matching heuristics (by reference, amount, fuzzy) | Import Bank Statement |
| **Chain of Responsibility** | Try heuristics in sequence, falling through to manual match | Import Bank Statement → Manual Match |
| **Pipeline** | Statement import flows through ordered stages | Import Bank Statement |
| **Aggregator** | Sum totals per participant for a period | Generate Settlement Summary |

### Pattern Details

**Chain of Responsibility**

- Intent: Pass a request along a chain of handlers until one handles it.
- Problem it solves: Multiple match heuristics must be tried in order, with unmatched entries falling through to manual reconciliation.
- Trade-offs:
    - ✅ Pro: Decoupled, independently orderable handlers.
    - ⚠️ Con: Harder to trace which handler ultimately matched.
- Where applied in this Epic: the auto-match heuristic chain in *Import Bank Statement*, ending at *Manual Reconciliation Match*.

**Strategy**

- Intent: Define a family of interchangeable matching algorithms.
- Problem it solves: Payments match invoices by different signals (reference, amount, fuzzy name).
- Trade-offs:
    - ✅ Pro: Add or tune heuristics independently of the chain.
    - ⚠️ Con: Extra indirection and configuration.
- Where applied in this Epic: each matching heuristic used during reconciliation.

## ✅ Definition of Done

- [ ] Does it work as the user expects?
- [ ] Is invalid input handled?
- [ ] Is this Production-ready?
