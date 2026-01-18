<?php

namespace App\Domain\Article\ValueObject;

class Language
{
    private string $code;

    private const ALLOWED = ['en', 'fr', 'de', 'es', 'ar'];

    public function __construct(string $code)
    {
        $code = strtolower(trim($code));
        if (!in_array($code, self::ALLOWED, true)) {
            throw new \InvalidArgumentException("Invalid language code: $code");
        }

        $this->code = $code;
    }

    public function __toString(): string
    {
        return $this->code;
    }

}