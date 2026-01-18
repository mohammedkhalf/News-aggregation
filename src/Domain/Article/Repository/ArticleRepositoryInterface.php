<?php

declare(strict_types=1);

namespace App\Domain\Article\Repository;

use App\Domain\Article\Article;

/**
 * Repository interface for Article domain entity
 */
interface ArticleRepositoryInterface
{
    public function findByExternalId(string $externalId): ?Article;

    public function upsert(Article $article): void;

    public function findById(string $id): ?Article;

    /**
     * @param array{language?: string, source?: string, sortBy?: string, sortOrder?: string} $filters
     * @return array<int, Article>
     */
    public function findAllWithFilters(array $filters, int $limit, int $offset): array;

    /**
     * @param array{language?: string, source?: string} $filters
     */
    public function countWithFilters(array $filters): int;
}