<?php

declare(strict_types=1);

namespace App\UI\DTO;

/**
 * Response DTO for article list endpoint
 */
class ArticleListResponse
{
    /**
     * @param array<int, array<string, string|null>> $data
     */
    public function __construct(
        public readonly array $data,
        public readonly int $total,
        public readonly int $page,
        public readonly int $limit,
        public readonly int $pages
    ) {
    }

    /**
     * @return array{data: array<int, array<string, string|null>>, meta: array{total: int, page: int, limit: int, pages: int}}
     */
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