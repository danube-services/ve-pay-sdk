# ve-pay-sdk

Polymorphic SDK gateway core for Venezuelan bank APIs.

## Install

```bash
composer require danube/ve-pay-sdk
```

## Usage

```php
use Danube\VePaySdk\Gateway\VePaySdk;
use Danube\VePaySdk\DTOs\PaymentValidationRequest;

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

match ($result->status) {
    PaymentStatus::Matched           => 'Pago confirmado',
    PaymentStatus::NotMatched        => 'Pago no encontrado',
    PaymentStatus::AlreadyReconciled => 'Ya fue conciliado',
    PaymentStatus::Error             => 'Error: ' . $result->message,
};
```

## Available drivers

| Bank | Package |
|------|---------|
| BNC | `danube/ve-pay-sdk-bnc` (coming soon) |
| BDV | `danube/ve-pay-sdk-bdv` (coming soon) |
| Mercantil | `danube/ve-pay-sdk-mercantil` (coming soon) |

## Development

```bash
composer install
./vendor/bin/pest                   # run tests
composer lint                       # code style check
composer format                     # auto-fix code style
```

## Requirements

- PHP 8.2+
