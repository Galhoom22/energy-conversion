# Project: Energy Conversion & Metering Platform
Generated: 2025-11-16T01:48:03.286649Z

This document contains 20 APIs with specific, varied user stories and full examples.

---

## API 01 — Register Meter
**Endpoint:** `POST /api/v1/energy_conversion/register-meter`

### User Story
As a **Field Operator**, I want to register meter so that add meter to system with metadata.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.286669Z",
  "data": {
    "example": "payload for api 1"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1001",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.286699Z",
  "details": {
    "result": "Outcome for api 1"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 02 — Bulk Import Meter Registrations
**Endpoint:** `POST /api/v1/energy_conversion/bulk-import-meter-registrations`

### User Story
As a **Field Operator**, I want to bulk import meter registrations so that onboard many meters from CSV.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.286723Z",
  "data": {
    "example": "payload for api 2"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1002",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.286737Z",
  "details": {
    "result": "Outcome for api 2"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 03 — Ingest Readings Batch
**Endpoint:** `POST /api/v1/energy_conversion/ingest-readings-batch`

### User Story
As a **Metering System**, I want to ingest readings batch so that store consumption readings.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.286755Z",
  "data": {
    "example": "payload for api 3"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1003",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.286769Z",
  "details": {
    "result": "Outcome for api 3"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 04 — Correct Reading
**Endpoint:** `POST /api/v1/energy_conversion/correct-reading`

### User Story
As a **Operator**, I want to correct reading so that fix erroneous historical readings with audit reason.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.286788Z",
  "data": {
    "example": "payload for api 4"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1004",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.286803Z",
  "details": {
    "result": "Outcome for api 4"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 05 — Create Tariff
**Endpoint:** `POST /api/v1/energy_conversion/create-tariff`

### User Story
As a **Billing Admin**, I want to create tariff so that define time-of-use pricing rules.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.286820Z",
  "data": {
    "example": "payload for api 5"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1005",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.286834Z",
  "details": {
    "result": "Outcome for api 5"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 06 — Simulate Tariff On Meter
**Endpoint:** `POST /api/v1/energy_conversion/simulate-tariff-on-meter`

### User Story
As a **Billing Admin**, I want to simulate tariff on meter so that estimate bills under tariff changes.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.286851Z",
  "data": {
    "example": "payload for api 6"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1006",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.286864Z",
  "details": {
    "result": "Outcome for api 6"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 07 — Run Billing Job
**Endpoint:** `POST /api/v1/energy_conversion/run-billing-job`

### User Story
As a **Billing System**, I want to run billing job so that generate invoices for period.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.286999Z",
  "data": {
    "example": "payload for api 7"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1007",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287020Z",
  "details": {
    "result": "Outcome for api 7"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 08 — Import Bank Statement For Reconciliation
**Endpoint:** `POST /api/v1/energy_conversion/import-bank-statement-for-reconciliation`

### User Story
As a **Finance**, I want to import bank statement for reconciliation so that match payments to invoices.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287139Z",
  "data": {
    "example": "payload for api 8"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1008",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287178Z",
  "details": {
    "result": "Outcome for api 8"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 09 — Manual Reconciliation Match
**Endpoint:** `POST /api/v1/energy_conversion/manual-reconciliation-match`

### User Story
As a **Operator**, I want to manual reconciliation match so that link payment to invoice when heuristics fail.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287200Z",
  "data": {
    "example": "payload for api 9"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1009",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287215Z",
  "details": {
    "result": "Outcome for api 9"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 10 — Export Regulatory Report
**Endpoint:** `POST /api/v1/energy_conversion/export-regulatory-report`

### User Story
As a **Regulatory**, I want to export regulatory report so that produce regulator-required files.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287235Z",
  "data": {
    "example": "payload for api 10"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1010",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287250Z",
  "details": {
    "result": "Outcome for api 10"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 11 — Query Customer Consumption
**Endpoint:** `POST /api/v1/energy_conversion/query-customer-consumption`

### User Story
As a **Customer Service**, I want to query customer consumption so that answer billing inquiries.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287279Z",
  "data": {
    "example": "payload for api 11"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1011",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287293Z",
  "details": {
    "result": "Outcome for api 11"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 12 — Deactivate Meter
**Endpoint:** `POST /api/v1/energy_conversion/deactivate-meter`

### User Story
As a **Meter Admin**, I want to deactivate meter so that stop new readings and billings.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287311Z",
  "data": {
    "example": "payload for api 12"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1012",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287325Z",
  "details": {
    "result": "Outcome for api 12"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 13 — Generate Settlement Summary
**Endpoint:** `POST /api/v1/energy_conversion/generate-settlement-summary`

### User Story
As a **Settlement Admin**, I want to generate settlement summary so that calculate totals per participant.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287343Z",
  "data": {
    "example": "payload for api 13"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1013",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287366Z",
  "details": {
    "result": "Outcome for api 13"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 14 — Detect Negative Consumption
**Endpoint:** `POST /api/v1/energy_conversion/detect-negative-consumption`

### User Story
As a **Ops**, I want to detect negative consumption so that flag anomalies for investigation.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287436Z",
  "data": {
    "example": "payload for api 14"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1014",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287454Z",
  "details": {
    "result": "Outcome for api 14"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 15 — Subscribe Webhooks For Invoices
**Endpoint:** `POST /api/v1/energy_conversion/subscribe-webhooks-for-invoices`

### User Story
As a **Integration**, I want to subscribe webhooks for invoices so that receive invoice.created events.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287472Z",
  "data": {
    "example": "payload for api 15"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1015",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287486Z",
  "details": {
    "result": "Outcome for api 15"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 16 — Get Reading Telemetry
**Endpoint:** `POST /api/v1/energy_conversion/get-reading-telemetry`

### User Story
As a **Support**, I want to get reading telemetry so that diagnose meter behavior.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287512Z",
  "data": {
    "example": "payload for api 16"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1016",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287526Z",
  "details": {
    "result": "Outcome for api 16"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 17 — Upload Calibration Data
**Endpoint:** `POST /api/v1/energy_conversion/upload-calibration-data`

### User Story
As a **Engineer**, I want to upload calibration data so that apply meter calibration adjustments.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287552Z",
  "data": {
    "example": "payload for api 17"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1017",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287571Z",
  "details": {
    "result": "Outcome for api 17"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 18 — Get Audit Trail For Meter
**Endpoint:** `POST /api/v1/energy_conversion/get-audit-trail-for-meter`

### User Story
As a **Auditor**, I want to get audit trail for meter so that review changes to meter records.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287595Z",
  "data": {
    "example": "payload for api 18"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1018",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287610Z",
  "details": {
    "result": "Outcome for api 18"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 19 — Manage Tariff Simulations
**Endpoint:** `POST /api/v1/energy_conversion/manage-tariff-simulations`

### User Story
As a **Admin**, I want to manage tariff simulations so that batch-run scenarios.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287756Z",
  "data": {
    "example": "payload for api 19"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1019",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287773Z",
  "details": {
    "result": "Outcome for api 19"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

## API 20 — Export Billing Csv
**Endpoint:** `POST /api/v1/energy_conversion/export-billing-CSV`

### User Story
As a **Operator**, I want to export billing CSV so that archive billing outputs.

### Authorization / Preconditions
- OAuth2 Bearer token. Required scope: `energy_conversion.write`.
- Organization context required via `organizationId` in payload.

### Request Schema (example)
```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.287789Z",
  "data": {
    "example": "payload for api 20"
  }
}
```

### Response Schema (example)
```json
{
  "id": "ene_1020",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.287802Z",
  "details": {
    "result": "Outcome for api 20"
  }
}
```

### Business Rules & Side Effects
- Validate `organizationId` and `requesterId` have proper permissions.
- Enforce idempotency via `Idempotency-Key` header for mutating operations.
- Emit domain event and write audit record.

### Errors
- `400` — validation errors with field-level messages.
- `401` — unauthorized.
- `403` — forbidden (insufficient scope).
- `409` — conflict (duplicate idempotency or concurrent update).

---

