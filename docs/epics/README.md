# 🗂️ Epics

This folder breaks the [Complete API specification](../Complete_APIs.md) into delivery-ready epics. Each epic groups related APIs into features and user stories with acceptance criteria, tasks, and a definition of done.

| Epic | Theme | APIs Covered |
| :--: | ----- | ------------ |
| [01 — Meter Management](epic-01-meter-management.md) | Onboard, calibrate, and retire meters | Register Meter · Bulk Import · Upload Calibration · Deactivate Meter |
| [02 — Readings & Consumption](epic-02-readings-and-consumption.md) | Capture, correct, and surface consumption | Ingest Readings · Correct Reading · Query Consumption · Reading Telemetry · Detect Negative Consumption |
| [03 — Tariff & Billing](epic-03-tariff-and-billing.md) | Pricing, simulation, invoicing, export | Create Tariff · Simulate Tariff · Manage Simulations · Run Billing Job · Export Billing CSV |
| [04 — Reconciliation & Settlement](epic-04-reconciliation-and-settlement.md) | Match payments and settle participants | Import Bank Statement · Manual Reconciliation Match · Generate Settlement Summary |
| [05 — Reporting, Audit & Integration](epic-05-reporting-audit-and-integration.md) | Compliance, auditability, webhooks | Export Regulatory Report · Get Audit Trail · Subscribe Webhooks |

All 20 APIs from the source specification are covered across these five epics.
