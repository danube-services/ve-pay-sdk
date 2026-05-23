<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\Document;

test('Document parses cedula V format', function () {
    $doc = Document::fromString('V27037606');

    expect($doc->type)->toBe('V')
        ->and($doc->number)->toBe('27037606');
});

test('Document parses extranjero E format', function () {
    $doc = Document::fromString('E12345678');

    expect($doc->type)->toBe('E')
        ->and($doc->number)->toBe('12345678');
});

test('Document parses juridico J format', function () {
    $doc = Document::fromString('J012345678');

    expect($doc->type)->toBe('J')
        ->and($doc->number)->toBe('012345678');
});

test('Document normalizes type to uppercase', function () {
    $doc = Document::fromString('v27037606');

    expect($doc->type)->toBe('V');
});

test('Document toString returns original format', function () {
    $doc = Document::fromString('V27037606');

    expect($doc->toString())->toBe('V27037606');
});

test('Document rejects empty string', function () {
    Document::fromString('');
})->throws(InvalidArgumentException::class, 'Document cannot be empty.');

test('Document rejects only letters', function () {
    Document::fromString('V');
})->throws(InvalidArgumentException::class);

test('Document rejects format without type prefix', function () {
    Document::fromString('27037606');
})->throws(InvalidArgumentException::class);

test('Document is readonly', function () {
    $doc = Document::fromString('V27037606');

    $reflection = new ReflectionClass($doc);

    expect($reflection->isReadOnly())->toBeTrue();
});
