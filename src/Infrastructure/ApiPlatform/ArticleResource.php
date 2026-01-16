<?php

namespace App\Infrastructure\ApiPlatform;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domain\Article\Article;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()
    ],
    paginationEnabled: true
)]
class ArticleResource
{
    public string $id;
    public string $title;
    public ?string $description;
    public ?string $content;
    public string $source;
    public string $url;
    public ?string $imageUrl;
    public string $language;
    public \DateTimeImmutable $publishedAt;
}