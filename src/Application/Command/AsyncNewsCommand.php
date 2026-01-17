<?php

namespace App\Application\Command;

class AsyncNewsCommand
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $language = null,
        public ?string $fromDate = null,
        public ?string $toDate = null
    ) {}

}