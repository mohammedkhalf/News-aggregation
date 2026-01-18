<?php

namespace App\UI\DTO;

class ArticleListResponse
{
    public function __construct(
        public readonly array $data,
        public readonly int $total,
        public readonly int $page,
        public readonly int $limit,
        public readonly int $pages
    ) {}

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'meta' => [
                'total' => $this->total,
                'page' => $this->page,
                'limit' => $this->limit,
                'pages' => $this->pages,
            ],
        ];
    }
}