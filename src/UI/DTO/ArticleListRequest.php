<?php

declare(strict_types=1);

namespace App\UI\DTO;

/**
 * Request DTO for article list endpoint
 */
class ArticleListRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 20,
        public readonly ?string $language = null,
        public readonly ?string $source = null,
        public readonly string $sortBy = 'publishedAt',
        public readonly string $sortOrder = 'DESC'
    ) {
    }

    /**
     * @param array<string, mixed> $queryParams
     */
    public static function fromRequest(array $queryParams): self
    {
        return new self(
            page: max(1, (int) ($queryParams['page'] ?? 1)),
            limit: max(1, min(100, (int) ($queryParams['limit'] ?? 20))),
            language: isset($queryParams['language']) && is_string($queryParams['language'])
                ? $queryParams['language']
                : null,
            source: isset($queryParams['source']) && is_string($queryParams['source'])
                ? $queryParams['source']
                : null,
            sortBy: isset($queryParams['sortBy']) && is_string($queryParams['sortBy'])
                ? $queryParams['sortBy']
                : 'publishedAt',
            sortOrder: isset($queryParams['sortOrder']) && is_string($queryParams['sortOrder'])
                && strtoupper($queryParams['sortOrder']) === 'ASC'
                ? 'ASC'
                : 'DESC'
        );
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}