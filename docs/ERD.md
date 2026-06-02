# 🗃️ Entity Relationship Diagram (Lightweight)

> **Scope:** core entities and their relationships only — **keys, not full columns**.
> Field-level detail (individual columns, nullability, indexes) is defined **per slice** in migrations as each [epic](epics/README.md) is implemented. This diagram exists to keep the *structural* decisions (entities, relationships, foreign keys) intentional, while column detail stays emergent (per the project's convention-first / YAGNI standards).

## Diagram

```mermaid
erDiagram
    ORGANIZATION ||--o{ METER : owns
    ORGANIZATION ||--o{ TARIFF : defines
    ORGANIZATION ||--o{ PAYMENT : receives
    ORGANIZATION ||--o{ WEBHOOK_SUBSCRIPTION : registers
    ORGANIZATION ||--o{ USER : employs

    METER ||--o{ READING : produces
    METER ||--o{ CALIBRATION : "is calibrated by"
    METER ||--o{ INVOICE : "is billed in"
    METER ||--o{ TARIFF_SIMULATION : "is simulated in"

    TARIFF ||--o{ INVOICE : prices
    TARIFF ||--o{ TARIFF_SIMULATION : models

    INVOICE ||--o{ RECONCILIATION_MATCH : "is matched in"
    PAYMENT ||--o{ RECONCILIATION_MATCH : "is matched in"
    BANK_STATEMENT ||--o{ PAYMENT : imports

    SETTLEMENT ||--o{ SETTLEMENT_LINE : summarizes
    INVOICE ||--o{ SETTLEMENT_LINE : "contributes to"

    ORGANIZATION {
        id PK
    }
    USER {
        id PK
        organization_id FK
    }
    METER {
        id PK
        organization_id FK
        status "active | inactive"
    }
    READING {
        id PK
        meter_id FK
    }
    CALIBRATION {
        id PK
        meter_id FK
    }
    TARIFF {
        id PK
        organization_id FK
    }
    TARIFF_SIMULATION {
        id PK
        tariff_id FK
        meter_id FK
    }
    INVOICE {
        id PK
        meter_id FK
        tariff_id FK
        organization_id FK
    }
    BANK_STATEMENT {
        id PK
        organization_id FK
    }
    PAYMENT {
        id PK
        organization_id FK
        bank_statement_id FK
    }
    RECONCILIATION_MATCH {
        id PK
        invoice_id FK
        payment_id FK
        matched_by "auto | manual"
    }
    SETTLEMENT {
        id PK
        organization_id FK
        period
    }
    SETTLEMENT_LINE {
        id PK
        settlement_id FK
        invoice_id FK
    }
    WEBHOOK_SUBSCRIPTION {
        id PK
        organization_id FK
        event "e.g. invoice.created"
    }
    AUDIT_RECORD {
        id PK
        organization_id FK
        auditable_type "polymorphic"
        auditable_id "polymorphic"
    }
```

## Notes

- **`ORGANIZATION`** is the multi-tenant root — almost every entity carries an `organization_id` and all access is scoped to it (the `organizationId` in every request payload).
- **`AUDIT_RECORD`** is intentionally **cross-cutting / polymorphic** (`auditable_type` + `auditable_id`) rather than linked to one table, since every mutating endpoint writes an audit record. It is drawn without hard relationship lines to avoid implying a single owner.
- **`RECONCILIATION_MATCH`** is the join between `PAYMENT` and `INVOICE`; `matched_by` distinguishes auto-matched vs. manual (Epic 04).
- **`SETTLEMENT` / `SETTLEMENT_LINE`** model per-participant aggregation for a period (Epic 04).
- **`WEBHOOK_SUBSCRIPTION`** drives outbound `invoice.created` delivery (Epic 05).

## Entity → Epic mapping

| Entity | Primary Epic |
| ------ | ------------ |
| Meter · Reading · Calibration | 01 Meter Management · 02 Readings |
| Tariff · TariffSimulation · Invoice | 03 Tariff & Billing |
| BankStatement · Payment · ReconciliationMatch · Settlement | 04 Reconciliation & Settlement |
| WebhookSubscription · AuditRecord | 05 Reporting, Audit & Integration |

> Columns beyond the keys shown here are deliberately omitted — they are added in migrations when the relevant slice is built.
