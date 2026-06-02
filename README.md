<h1 align="center">⚡ Energy Conversion & Metering Platform</h1>

<p align="center">
  A Laravel-powered platform for <strong>energy metering</strong>, <strong>tariff management</strong>, <strong>billing</strong>, <strong>reconciliation</strong>, and <strong>regulatory reporting</strong>.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Pest-4-5B21B6?style=flat-square&logo=pest&logoColor=white" alt="Pest 4">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/License-MIT-22C55E?style=flat-square" alt="MIT License">
</p>

---

## 📖 Overview

This platform exposes a versioned REST API for the full energy metering lifecycle — registering meters, ingesting consumption readings, defining tariffs, running billing jobs, reconciling payments, and producing regulatory and settlement outputs.

## 🧰 Tech Stack

| Layer | Technology |
| ----- | ---------- |
| Language | PHP 8.4 |
| Framework | Laravel 13 |
| Testing | Pest 4 |
| Frontend | Tailwind CSS 4 |
| Code Style | Laravel Pint |
| Tooling | Laravel Boost · Pail · Prompts |

## 🏛️ Architecture

The project follows a **domain-oriented modular monolith** — a single Laravel application organized by business domain rather than by technical type.

**Convention-first.** The default for every endpoint is plain Laravel: a thin **Controller** → **Form Request** (validation + authorization) → **Eloquent** → **API Resource** (response shaping). Heavier building blocks (dedicated Action classes, DTOs, domain events, Strategy/Pipeline, queued jobs) are introduced **only when a specific use case justifies them** — never preemptively. Domains stay decoupled by communicating through events rather than reaching into each other's internals.

```
app/
├── Domain/                  # domain code, grouped by business domain
│   ├── Metering/            # Epics 01 + 02
│   ├── Billing/             # Epic 03
│   ├── Reconciliation/      # Epic 04
│   └── Compliance/          # Epic 05
│       ├── Models/          # Eloquent models (the default home for domain logic)
│       ├── Actions/         # added only when a use case has real orchestration
│       ├── Events/          # added only when another domain must react
│       └── Data/            # DTOs / value objects, when typed input adds value
├── Http/
│   └── Api/V1/              # versioned HTTP layer
│       ├── Controllers/     # thin: authorize → validate → respond
│       ├── Requests/        # Form Requests (validation + authorization)
│       └── Resources/       # API Resources (response shaping)
└── Support/                 # cross-cutting: idempotency, audit recorder

routes/
├── api.php                  # loads the versioned domain route files
└── api/v1/                  # one route file per domain
```

**Why this architecture:** it maps 1:1 to the [epics](docs/epics/README.md), keeps domain boundaries clear without extra dependencies, and stays easy to evolve into separate modules or services later if scale demands it. See the [epics design-patterns guidance](docs/epics/README.md#-design-patterns) for when to escalate beyond the default.

**Data model:** core entities and their relationships are mapped in the lightweight [ERD](docs/ERD.md) (keys only) — column-level detail is added per slice in migrations as each epic is built.

## 🚀 Getting Started

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Run migrations (includes Sanctum's personal_access_tokens table)
php artisan migrate

# 4. Start the dev environment (server, queue, logs, vite)
composer run dev
```

## 🔌 API

All endpoints are versioned under `/api/v1/energy_conversion/`. Routes live in `routes/api.php` and are served under the `/api` prefix with the `api` middleware group.

### 🔑 Authentication

The API uses **Laravel Sanctum** bearer tokens. Send the token on every request:

```http
Authorization: Bearer <token>
```

### 🔐 Conventions

- **Auth** — Bearer token via [Laravel Sanctum](https://laravel.com/docs/sanctum). Mutating endpoints require the `energy_conversion.write` scope.
- **Organization context** — every request must include an `organizationId` in the payload.
- **Idempotency** — mutating operations honor the `Idempotency-Key` header.
- **Side effects** — successful operations emit a domain event and write an audit record.

<details>
<summary><strong>📦 Standard request envelope</strong></summary>

```json
{
  "organizationId": "org_1",
  "requesterId": "user_123",
  "timestamp": "2025-11-16T01:48:03.286669Z",
  "data": {
    "example": "payload"
  }
}
```
</details>

<details>
<summary><strong>📬 Standard response envelope</strong></summary>

```json
{
  "id": "ene_1001",
  "status": "SUCCESS",
  "processedAt": "2025-11-16T01:48:03.286699Z",
  "details": {
    "result": "Outcome"
  }
}
```
</details>

### ⚠️ Error Codes

| Code | Meaning |
| :--: | ------- |
| `400` | Validation errors with field-level messages |
| `401` | Unauthorized |
| `403` | Forbidden (insufficient scope) |
| `409` | Conflict (duplicate idempotency key or concurrent update) |

### 📍 Endpoints

| # | Endpoint | Actor | Purpose |
| :--: | -------- | ----- | ------- |
| 01 | `POST /register-meter` | Field Operator | Add a meter to the system with metadata |
| 02 | `POST /bulk-import-meter-registrations` | Field Operator | Onboard many meters from CSV |
| 03 | `POST /ingest-readings-batch` | Metering System | Store consumption readings |
| 04 | `POST /correct-reading` | Operator | Fix erroneous historical readings with audit reason |
| 05 | `POST /create-tariff` | Billing Admin | Define time-of-use pricing rules |
| 06 | `POST /simulate-tariff-on-meter` | Billing Admin | Estimate bills under tariff changes |
| 07 | `POST /run-billing-job` | Billing System | Generate invoices for a period |
| 08 | `POST /import-bank-statement-for-reconciliation` | Finance | Match payments to invoices |
| 09 | `POST /manual-reconciliation-match` | Operator | Link payment to invoice when heuristics fail |
| 10 | `POST /export-regulatory-report` | Regulatory | Produce regulator-required files |
| 11 | `POST /query-customer-consumption` | Customer Service | Answer billing inquiries |
| 12 | `POST /deactivate-meter` | Meter Admin | Stop new readings and billings |
| 13 | `POST /generate-settlement-summary` | Settlement Admin | Calculate totals per participant |
| 14 | `POST /detect-negative-consumption` | Ops | Flag anomalies for investigation |
| 15 | `POST /subscribe-webhooks-for-invoices` | Integration | Receive `invoice.created` events |
| 16 | `POST /get-reading-telemetry` | Support | Diagnose meter behavior |
| 17 | `POST /upload-calibration-data` | Engineer | Apply meter calibration adjustments |
| 18 | `POST /get-audit-trail-for-meter` | Auditor | Review changes to meter records |
| 19 | `POST /manage-tariff-simulations` | Admin | Batch-run tariff scenarios |
| 20 | `POST /export-billing-CSV` | Operator | Archive billing outputs |

> 📚 Full request/response schemas, business rules, and error details live in [`docs/Complete_APIs.md`](docs/Complete_APIs.md).

## 🗂️ Epics

The API is grouped into five delivery-ready epics — each with features, user stories, acceptance criteria, and tasks. Browse them in [`docs/epics`](docs/epics/README.md).

| Epic | Theme | APIs |
| :--: | ----- | :--: |
| [01 — Meter Management](docs/epics/epic-01-meter-management.md) | Onboard, calibrate, and retire meters | 4 |
| [02 — Readings & Consumption](docs/epics/epic-02-readings-and-consumption.md) | Capture, correct, and surface consumption | 5 |
| [03 — Tariff & Billing](docs/epics/epic-03-tariff-and-billing.md) | Pricing, simulation, invoicing, export | 5 |
| [04 — Reconciliation & Settlement](docs/epics/epic-04-reconciliation-and-settlement.md) | Match payments and settle participants | 3 |
| [05 — Reporting, Audit & Integration](docs/epics/epic-05-reporting-audit-and-integration.md) | Compliance, auditability, webhooks | 3 |

## 🧪 Testing

```bash
# Run the full suite
php artisan test --compact

# Filter to a single test
php artisan test --compact --filter=testName
```

## 🎨 Code Style

```bash
vendor/bin/pint --format agent
```

## 📄 License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
