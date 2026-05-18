# ve-pay-sdk Core Contracts Reference

This document describes the interfaces and DTOs a bank driver must implement.

## Required dependency

```json
{
    "require": {
        "danube/ve-pay-sdk": "^1.0"
    }
}
```

## Interface to implement

### `Danube\VePaySdk\Contracts\PaymentValidator`

```php
interface PaymentValidator
{
    public function validate(PaymentValidationRequest $request): PaymentValidationResult;
}
```

## Request DTO

### `Danube\VePaySdk\DTOs\PaymentValidationRequest`

| Field | Type | Required | Description |
|-------|------|:---:|-------------|
| `reference` | `string` | Yes | Payment reference number |
| `amount` | `string` | Yes | Amount with dot decimal (e.g. "120.00") |
| `date` | `string` | Yes | Payment date (YYYY-MM-DD) |
| `payerPhone` | `?string` | No | Payer phone number |
| `destPhone` | `?string` | No | Destination phone number |
| `bankCode` | `?string` | No | Origin bank code |
| `metadata` | `array` | No | Bank-specific fields |

## Response DTO

### `Danube\VePaySdk\DTOs\PaymentValidationResult`

```php
readonly class PaymentValidationResult
{
    public PaymentStatus $status,
    public ?string $amount,
    public ?string $reference,
    public ?string $date,
    public ?string $debtorId,
    public ?string $debtorType,
    public string  $message,
    public array   $rawResponse,
}
```

## Status Enum

### `Danube\VePaySdk\DTOs\PaymentStatus`

- `matched` — payment exists and amounts match
- `not_matched` — payment not found or data mismatch
- `already_reconciled` — already reconciled before
- `error` — bank or gateway error

## Error handling rules

1. **Transport errors** (HTTP 5xx, timeout, DNS, auth failure) → throw `GatewayException` (from `Danube\VePaySdk\Gateway\`)
2. **Business errors** (payment not found, already reconciled, bad data) → return `PaymentValidationResult` with appropriate status
3. `not_matched` is a successful call — bank confirmed payment doesn't exist
4. Always populate `$result->rawResponse` with the original bank response for debugging

## Bank-specific mapping (metadata convention)

Each driver reads from `$request->metadata[]` for bank-specific fields. Document what keys your driver expects.

Examples:

| Bank | Common metadata keys |
|------|---------------------|
| BDV | `cedulaPagador`, `reqCed` (bool), `telefonoPagador`, `telefonoDestino` |
| BNC | `AccountNumber`, `ClientID`, `ChildClientID?`, `BranchID?` |
| Mercantil | `searchMode` (`full` or `suffix`), `clientId` |
