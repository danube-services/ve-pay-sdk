# AGENTS.md

## Project Identity
Polymorphic SDK gateway **core** for Venezuelan bank APIs.
PHP 8.2+, Composer (PSR-4), tested with Pest.

## Architecture
- **THIS REPO = contracts only.** Defines interfaces, DTOs, enums, and shared gateway logic.
- **Bank drivers = external Composer packages** (e.g., `ve-pay-sdk-bnc`).
  Each requires `danube/ve-pay-sdk` and implements its interfaces.
- **NO bank-specific code allowed here.** If it talks to a real bank API, it lives in a driver package.

## Spec-Driven Development
- Specs live in `docs/superpowers/specs/` as Markdown files. **Specs are written before code.**
- Each spec defines: interface methods, DTO shapes, input/output contracts, behavior.
- Tests derive from specs — spec is the source of truth.
- Workflow: `write spec → review → implement interface/DTO → write Pest tests → implement`

## Directory Structure
```
docs/superpowers/specs/   # Markdown specs (one per domain/driver)
src/
  Contracts/              # PHP interfaces (PaymentValidator, future: TransferProcessor, etc.)
  DTOs/                   # Shared value objects (PaymentStatus, PaymentValidationRequest/Result)
  Gateway/                # SDK facade (VePaySdk) + domain handlers (Conciliation, future: Transfers, etc.)
                          # Also: HTTP client, auth signing, error normalization
  Resolver/               # Driver discovery / registration
tests/
  Unit/
    DTOs/
    Gateway/
  Feature/
```

## Current Implementation (Phase 1 — P2P Payment Validation)

### Contracts
- `PaymentValidator` — interface with `validate(PaymentValidationRequest): PaymentValidationResult`

### DTOs
- `PaymentStatus` — enum: `matched`, `not_matched`, `already_reconciled`, `error`
- `PaymentValidationRequest` — readonly class: `reference`, `amount`, `date`, `payerPhone?`, `destPhone?`, `bankCode?`, `metadata[]`
- `PaymentValidationResult` — readonly class: `status`, `amount?`, `reference?`, `date?`, `debtorId?`, `debtorType?`, `message`, `rawResponse[]`

### Gateway
- `VePaySdk` — facade class. Receives a `PaymentValidator` driver, exposes domain handlers.
- `Conciliation` — domain handler. `conciliate()` delegates to `$driver->validate()`.

### API Usage
```php
$sdk = new VePaySdk(new BdvDriver($configs));
$request = new PaymentValidationRequest(
    reference: '12345678',
    amount: '120.00',
    date: '2023-02-12',
    payerPhone: '04127141363',
    bankCode: '0102',
    metadata: ['reqCed' => false, 'cedulaPagador' => 'V27037606'],
);
$result = $sdk->conciliation->conciliate($request);
```

## Commands
```
composer install
./vendor/bin/pest                              # all tests
./vendor/bin/pest --filter=SomeTest            # single test
composer lint                                  # php-cs-fixer --dry-run
composer format                                # php-cs-fixer fix
```

## Key Conventions
- PHP 8.2+ — use readonly classes, enums, intersection types
- PSR-12 via PHP-CS-Fixer
- Pest for testing — `tests/Unit` and `tests/Feature`
- Laravel-compatible (provide a ServiceProvider + optional Facade), but zero framework dependency
- Shared gateway logic (HTTP, auth, error mapping) lives in `Gateway/` once — never duplicated in drivers
- Driver discovery via `Resolver/` (service container tag or manual registration)
- Error handling: transport errors throw `GatewayException`; business errors returned in `PaymentStatus`

## Bank Roadmap
1. BNC (Banco Nacional de Crédito)
2. BDV (Banco de Venezuela)
3. Mercantil
