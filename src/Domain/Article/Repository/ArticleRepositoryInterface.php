<?php

namespace App\Domain\Article\Repository;

use App\Domain\Article\Article;

interface ArticleRepositoryInterface
{

    public function findByExternalId(string $externalId): ?Article;

    public function upsert(Article $article): void;

    public function findById(string $id): ?Article;

    public function findAllWithFilters(array $filters, int $limit, int $offset): array;

    public function countWithFilters(array $filters): int;


}