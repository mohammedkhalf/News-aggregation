<?php

namespace App\Application\Command;

class SyncNewsCommand
{
    public function __construct(
        public readonly ?string $keyword = null,
        public readonly ?string $language = null,
        public readonly ?string $fromDate = null,
        public readonly ?string $toDate = null
    ) {}

}