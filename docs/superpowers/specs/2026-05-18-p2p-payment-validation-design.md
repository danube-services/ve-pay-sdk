# P2P Payment Validation — Design Spec

**Date:** 2026-05-18
**Phase:** 1
**Scope:** Contracts only — interfaces, DTOs, enums. No bank implementations.

---

## 1. Context

This repo (`danube/ve-pay-sdk`) is the **polymorphic SDK core** for Venezuelan bank APIs. It defines interfaces, DTOs, enums, and shared gateway logic. Bank-specific implementations live in external driver packages (e.g., `ve-pay-sdk-bnc`).

The project is currently a fresh seed — no code, no composer.json, no tests.

### Bank APIs Analyzed

| Bank | Endpoint | Nature |
|------|----------|--------|
| **Mercantil** | `POST /search` | Search (multiple results) |
| **BNC** | `POST /api/Position/ValidateP2P` | Unit validation |
| **BDV** | `POST /getMovement/v2` | Unit validation |

**Phase 1 scope:** Unit validation only. Mercantil's driver will internally search but expose the same validation contract.

---

## 2. Architecture Decision: Enfoque B

**Core DTO + metadata extension.** A single request DTO with fields common to all 3 banks, plus an `array $metadata` for bank-specific fields. Each driver extracts what it needs from metadata.

### Rationale
- The 3 banks share a core: `reference`, `amount`, `date`, `payerPhone`, `bankCode`.
- Each has idiosyncratic fields: BDV (`reqCed`), BNC (`AccountNumber`, `ChildClientID`, `BranchID`), Mercantil (reference suffix mode).
- Metadata avoids DTO inflation and keeps the contract stable as new banks are added.
- Compatible with the resolver pattern (caller passes data → resolver picks driver → driver reads metadata).

---

## 3. Component Design

### 3.1 `PaymentStatus` — Enum

Normalizes all bank-native status codes into 4 business states.

```php
namespace Danube\VePaySdk\DTOs;

enum PaymentStatus: string
{
    case Matched           = 'matched';
    case NotMatched        = 'not_matched';
    case AlreadyReconciled = 'already_reconciled';
    case Error             = 'error';
}
```

**Mapping per bank:**

| Our Status | BDV | BNC | Mercantil |
|:-:|:-:|:-:|:-:|
| `matched` | `code: 1000` | `MovementExists: true` | Search found result |
| `not_matched` | `code: 1010` (not found) | `MovementExists: false` | Search empty |
| `already_reconciled` | `code: 1010` (already reconciled msg) | — (N/A) | — (TBD) |
| `error` | `code: 1010` (generic err) | HTTP non-2xx / exception | HTTP non-2xx / exception |

### 3.2 `PaymentValidationRequest` — DTO

```php
namespace Danube\VePaySdk\DTOs;

readonly class PaymentValidationRequest
{
    public function __construct(
        public string $reference,      // Required. Payment reference number.
        public string $amount,         // Required. Format: decimal with dot (e.g. "120.00").
        public string $date,           // Required. Date of payment (YYYY-MM-DD).
        public ?string $payerPhone,    // Payer's phone number (with country code for BNC).
        public ?string $destPhone,     // Destination/receiver phone number.
        public ?string $bankCode,      // Origin bank code (e.g. "0102", "191").
        public array   $metadata,      // Bank-specific data. Default empty array.
    ) {}
}
```

**Metadata contract per bank (informative — enforced by each driver):**

| Bank | Expected metadata keys |
|------|----------------------|
| BDV | `cedulaPagador`, `reqCed` (bool), `telefonoPagador`, `telefonoDestino` |
| BNC | `AccountNumber`, `ClientID`, `ChildClientID?`, `BranchID?`, `RequestDate` |
| Mercantil | `searchMode` (`full` or `suffix`), `clientId` (X-IBM-Client-Id) |

### 3.3 `PaymentValidationResult` — DTO

```php
namespace Danube\VePaySdk\DTOs;

readonly class PaymentValidationResult
{
    public function __construct(
        public PaymentStatus $status,
        public ?string $amount,         // Amount confirmed by the bank.
        public ?string $reference,      // Reference confirmed by the bank.
        public ?string $date,           // Movement date from the bank.
        public ?string $debtorId,       // Payer document ID (if returned).
        public ?string $debtorType,     // Payer document type (if returned).
        public string  $message,        // Human-readable message from bank or generated.
        public array   $rawResponse,    // Original bank response for debugging.
    ) {}
}
```

### 3.4 `PaymentValidator` — Interface

```php
namespace Danube\VePaySdk\Contracts;

use Danube\VePaySdk\DTOs\PaymentValidationRequest;
use Danube\VePaySdk\DTOs\PaymentValidationResult;

interface PaymentValidator
{
    public function validate(PaymentValidationRequest $request): PaymentValidationResult;
}
```

Every bank driver package MUST implement this interface.

---

## 4. Error Handling Strategy

### Two error layers

| Layer | What | How |
|-------|------|-----|
| **Transport** | Timeout, DNS failure, HTTP 5xx, invalid auth, TLS errors | `GatewayException` (thrown) |
| **Business** | Payment not found, already reconciled, amount mismatch | `PaymentValidationResult` with appropriate `PaymentStatus` (returned, never thrown) |

### Rules
- A `GatewayException` means "the bank didn't respond properly." The caller can retry or fail.
- A `PaymentValidationResult` with `status: error` means "the bank responded but the operation failed at business level."
- A `PaymentValidationResult` with `status: not_matched` is a **successful** call — the bank confirmed the payment doesn't exist.
- `already_reconciled` is a special case of `not_matched` — the payment exists but was already processed.

---

## 5. What This Phase Does NOT Cover

- Bank driver implementations (BNC, BDV, Mercantil live in their own packages)
- `Gateway/` HTTP client, auth, encryption (Mercantil AES, BDV API Key, BNC auth)
- `Resolver/` driver discovery and registration
- Search/batch reconciliation (Mercantil multi-result search is internal to its driver)
- "Sin referencia" validation mode
- Webhooks or async reconciliation
- Laravel ServiceProvider / Facade

---

## 6. File Layout

```
src/
  Contracts/
    PaymentValidator.php         # Interface
  DTOs/
    PaymentStatus.php            # Enum
    PaymentValidationRequest.php # Request DTO
    PaymentValidationResult.php  # Response DTO
tests/
  Unit/
    DTOs/
      PaymentStatusTest.php
      PaymentValidationRequestTest.php
      PaymentValidationResultTest.php
  Feature/                       # (empty for now — tests for driver implementations)
```

---

## 7. Tests (Pest)

### 7.1 `PaymentStatus` enum
- Has exactly 4 cases
- Each case returns correct string value
- `from()` / `tryFrom()` work as expected

### 7.2 `PaymentValidationRequest`
- Required fields (`reference`, `amount`, `date`) are properly set
- Optional fields default to `null`
- `metadata` defaults to empty array
- Readonly properties cannot be mutated

### 7.3 `PaymentValidationResult`
- All properties set correctly via constructor
- `status` is typed as `PaymentStatus`
- `rawResponse` and `message` are populated
- Readonly properties cannot be mutated

---

## 8. Dependencies

- PHP 8.2+
- Pest (dev dependency for testing)
- No framework dependencies
- No external packages required for contracts

---

## 9. Acceptance Criteria

- [ ] `PaymentStatus` enum exists with 4 cases
- [ ] `PaymentValidationRequest` readonly class with all fields
- [ ] `PaymentValidationResult` readonly class with all fields
- [ ] `PaymentValidator` interface with single `validate()` method
- [ ] All classes in correct PSR-4 namespace (`Danube\VePaySdk\...`)
- [ ] Pest tests pass for all DTOs and enum
- [ ] No bank-specific code anywhere
- [ ] PHP 8.2+ features used (readonly classes, enums)
- [ ] PSR-12 compliant (verified by PHP-CS-Fixer)
