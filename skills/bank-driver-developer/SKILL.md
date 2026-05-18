---
name: bank-driver-developer
description: "Desarrolla un paquete driver para un nuevo banco venezolano compatible con ve-pay-sdk. Usar cuando el usuario quiera crear, implementar o desarrollar un driver de conciliación P2P para un banco (BNC, BDV, Mercantil u otro). También cuando pida 'crear driver para banco X', 'implementar validador de pagos para X', o quiera agregar soporte para un nuevo banco en el SDK."
---

# Bank Driver Developer

Desarrolla paquetes driver de banco compatibles con el core `danube/ve-pay-sdk`. Cada driver implementa la interfaz `PaymentValidator` y mapea la API específica del banco a los DTOs normalizados.

## Antes de empezar

Lee `references/contracts.md` — contiene la interfaz, DTOs, enum y reglas de error que debes implementar. No improvises firmas ni nombres.

## Proceso de desarrollo

### 1. Analizar la documentación del banco

El usuario te dará la documentación de la API del banco (URL, PDF, Postman, o texto). Extrae:

- **Endpoint:** URL y método HTTP
- **Autenticación:** headers, API keys, cifrado (AES, SHA, etc.)
- **Request:** campos requeridos, formatos (fechas, decimales), tipos
- **Response:** estructura, códigos de éxito/error, campos devueltos
- **Casos especiales:** reintentos, ventanas de tiempo, datos interbancarios

Si la documentación está incompleta o es ambigua, pregunta al usuario antes de continuar. No asumas valores ni formatos.

### 2. Diseñar el mapeo

Haz explícito cómo cada campo del banco se mapea a nuestros contratos:

**Request mapping** (banco → `PaymentValidationRequest`):

| Campo del banco | Campo en nuestro DTO | Nota |
|----------------|---------------------|------|
| referencia | `$request->reference` | |
| importe | `$request->amount` | |
| fechaPago | `$request->date` | Debe ser YYYY-MM-DD |
| telefonoPagador | `$request->payerPhone` | |
| bancoOrigen | `$request->bankCode` | |
| (idiosincrático) | `$request->metadata['key']` | Solo campos sin equivalente en el DTO |

**Response mapping** (banco response → `PaymentValidationResult`):

| Respuesta banco | Nuestro status | Condición |
|----------------|:-:|---|
| `code: 1000` o `MovementExists: true` | `matched` | Éxito |
| `code: 1010` (no existe) o `MovementExists: false` | `not_matched` | No encontrado |
| Mensaje "ya conciliado" | `already_reconciled` | Duplicado |
| HTTP no 2xx o error irrecuperable | `error` | Error de transporte |
| HTTP error relacionado con auth o timeout | throw `GatewayException` | |

Crea una tabla de mapeo concreta para la respuesta. Siempre puebla `rawResponse` con los datos crudos.

**Metadata contract:** lista clara de qué keys espera tu driver en `$request->metadata`. Ejemplo para BDV:

```php
// Metadatos esperados por el driver BDV
[
    'reqCed'          => bool,    // requerido
    'cedulaPagador'   => string,  // requerido
    'telefonoPagador' => string,  // opcional (si no está en payerPhone)
    'telefonoDestino' => string,  // requerido
]
```

### 3. Crear el paquete

Estructura del driver:

```
ve-pay-sdk-{banco}/
├── composer.json
├── src/
│   └── {Banco}PaymentValidator.php   # Implementación del contrato
└── tests/
    └── {Banco}PaymentValidatorTest.php
```

**composer.json** mínimo:

```json
{
    "name": "danube/ve-pay-sdk-{banco}",
    "description": "{Banco} driver for ve-pay-sdk",
    "type": "library",
    "require": {
        "php": ">=8.2",
        "danube/ve-pay-sdk": "^1.0"
    },
    "require-dev": {
        "pestphp/pest": "^3.0"
    },
    "autoload": {
        "psr-4": {
            "Danube\\VePaySdk{Banco}\\": "src/"
        }
    }
}
```

Sustituye `{banco}` por el slug del banco (ej: `bdv`, `bnc`, `mercantil`) en nombre, namespace, y paths.

### 4. Implementar el validador

La clase debe:

1. **Declarar** `implements PaymentValidator`
2. **Recibir** configuraciones del banco en el constructor (apiKey, baseUrl, http client)
3. **Construir** el request HTTP a partir de `PaymentValidationRequest` + metadata
4. **Ejecutar** la petición HTTP
5. **Mapear** la respuesta a `PaymentValidationResult` según la tabla de mapeo
6. **Manejar** errores de transporte con `GatewayException`

```php
use Danube\VePaySdk\Contracts\PaymentValidator;
use Danube\VePaySdk\DTOs\PaymentValidationRequest;
use Danube\VePaySdk\DTOs\PaymentValidationResult;
use Danube\VePaySdk\DTOs\PaymentStatus;
use Danube\VePaySdk\Gateway\GatewayException;

readonly class BdvPaymentValidator implements PaymentValidator
{
    public function __construct(
        private HttpClientInterface $http,
        private string $apiKey,
        private string $baseUrl,
    ) {}

    public function validate(PaymentValidationRequest $request): PaymentValidationResult
    {
        // 1. Leer metadata específico del banco
        $cedula = $request->metadata['cedulaPagador'] ?? null;
        $reqCed = $request->metadata['reqCed'] ?? false;

        // 2. Construir payload según el formato del banco
        $payload = [
            'cedulaPagador'  => $cedula,
            'referencia'     => $request->reference,
            'importe'       => $request->amount,
            'fechaPago'     => $request->date,
            'bancoOrigen'   => $request->bankCode,
            'telefonoPagador' => $request->payerPhone,
            'reqCed'        => $reqCed,
        ];

        // 3. Hacer la petición HTTP
        try {
            $response = $this->http->post($this->baseUrl, [
                'headers' => ['X-API-Key' => $this->apiKey],
                'json' => $payload,
            ]);
        } catch (\Throwable $e) {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }

        // 4. Parsear respuesta
        $data = json_decode($response->getBody(), true);

        // 5. Mapear a nuestro resultado
        return match ($data['code']) {
            1000 => new PaymentValidationResult(
                status: PaymentStatus::Matched,
                amount: $data['data']['amount'] ?? null,
                reference: $data['data']['referencia'] ?? null,
                message: $data['message'],
                rawResponse: $data,
            ),
            1010 => $this->resolve1010Status($data),
            default => new PaymentValidationResult(
                status: PaymentStatus::Error,
                message: 'Unexpected response',
                rawResponse: $data,
            ),
        };
    }
}
```

Notas de implementación:
- Usa `readonly class` siempre
- Extrae lógica de mapeo a métodos privados si crece más de ~15 líneas
- El `HttpClientInterface` viene del core (futuro `Gateway/`). Si aún no existe, usa un PSR-18 `ClientInterface`.
- Nunca hardcodees URLs, API keys ni valores de prueba

### 5. Escribir tests

Usa Pest. Patrón: stubs manuales (anonymous classes) para el HTTP client, no mock frameworks externos.

Cubre al menos estos escenarios:
- **Pago encontrado**: retorna `PaymentStatus::Matched` con amount, reference, etc.
- **Pago no encontrado**: retorna `PaymentStatus::NotMatched`
- **Ya conciliado**: retorna `PaymentStatus::AlreadyReconciled`
- **Error de transporte**: lanza `GatewayException`
- **Error de negocio**: retorna `PaymentStatus::Error` (sin excepción)
- **Metadata se lee correctamente**: los campos del metadata llegan al payload

Ejemplo de stub HTTP:

```php
test('returns matched when bank confirms payment', function () {
    $http = new readonly class implements HttpClientInterface {
        public function post(string $url, array $options): ResponseInterface
        {
            return new Response(200, body: json_encode([
                'code' => 1000,
                'message' => 'OK',
                'data' => ['amount' => '120.00', 'referencia' => '12345678'],
                'status' => 200,
            ]));
        }
    };

    $validator = new BdvPaymentValidator($http, 'fake-key', 'https://qa.example.com');

    $result = $validator->validate(new PaymentValidationRequest(
        reference: '12345678',
        amount: '120.00',
        date: '2023-02-12',
        payerPhone: '04127141363',
        bankCode: '0102',
        metadata: ['reqCed' => false, 'cedulaPagador' => 'V27037606'],
    ));

    expect($result->status)->toBe(PaymentStatus::Matched)
        ->and($result->amount)->toBe('120.00');
});
```

### 6. Verificar

Antes de considerar el driver completo:
- [ ] `composer install` exitoso
- [ ] `./vendor/bin/pest` todos los tests pasan
- [ ] `composer lint` limpio (PSR-12)
- [ ] No hay código hardcodeado (URLs, claves, secretos)
- [ ] El metadata contract está documentado en el README del driver
- [ ] La tabla de mapeo de estados está documentada

## Restricciones

- **NO copies APIs de otros bancos** — cada banco tiene su propio endpoint, auth y payload
- **NO modifiques los contratos del core** — el driver implementa, no extiende
- **NO mezcles lógica de múltiples bancos** — un paquete = un banco
- **NO dependas de Laravel** — el driver debe funcionar sin framework
- **SÍ puedes usar un HTTP client PSR-18** (guzzlehttp/guzzle, symfony/http-client)
