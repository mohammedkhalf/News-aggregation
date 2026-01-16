<?php

namespace App\Domain\Article\ValueObject;

class Language
{
    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function __toString(): string
    {
        return $this->code;
    }

}