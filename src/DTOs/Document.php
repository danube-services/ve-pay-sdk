<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

readonly class Document
{
    public function __construct(
        public string $type,
        public string $number,
    ) {
    }

    public static function fromString(string $value): self
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('Document cannot be empty.');
        }

        $type = mb_substr($trimmed, 0, 1);
        $number = mb_substr($trimmed, 1);

        if (!ctype_alpha($type)) {
            throw new \InvalidArgumentException("Invalid document format: {$value}");
        }

        if ($number === '' || !ctype_digit($number)) {
            throw new \InvalidArgumentException("Invalid document format: {$value}");
        }

        return new self(strtoupper($type), $number);
    }

    public function toString(): string
    {
        return $this->type . $this->number;
    }
}
