<?php

namespace App\Domain\Article\Repository;

use App\Domain\Article\Article;

interface ArticleRepositoryInterface
{

    public function findByExternalId(string $externalId): ?Article;

    public function upsert(Article $article): void;

}