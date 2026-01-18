<?php

namespace App\Domain\Article\ValueObject;

class Source
{
    private string $name;

    public function __construct(string $name)
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('Source name cannot be empty');
        }

        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

}