<?php

namespace App\UI\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class SyncNewsRequest
{
    #[Groups(['sync_news'])]
    public ?string $keyword = null;

    #[Groups(['sync_news'])]
    public ?string $language = null;

    #[Groups(['sync_news'])]
    public ?string $from = null;

    #[Groups(['sync_news'])]
    public ?string $to = null;
}