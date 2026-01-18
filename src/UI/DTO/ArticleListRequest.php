<?php

namespace App\UI\DTO;

class ArticleListRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 20,
        public readonly ?string $language = null,
        public readonly ?string $source = null,
        public readonly ?string $sortBy = 'publishedAt',
        public readonly string $sortOrder = 'DESC'
    ) {}

    public static function fromRequest(array $queryParams): self
    {
        return new self(
            page: max(1, (int) ($queryParams['page'] ?? 1)),
            limit: max(1, min(100, (int) ($queryParams['limit'] ?? 20))),
            language: $queryParams['language'] ?? null,
            source: $queryParams['source'] ?? null,
            sortBy: $queryParams['sortBy'] ?? 'publishedAt',
            sortOrder: strtoupper($queryParams['sortOrder'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'
        );
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}