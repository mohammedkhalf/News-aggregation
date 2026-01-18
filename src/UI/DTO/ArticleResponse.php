<?php

namespace App\UI\DTO;

use App\Domain\Article\Article;

class ArticleResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $externalId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $content,
        public readonly string $source,
        public readonly string $url,
        public readonly ?string $imageUrl,
        public readonly string $language,
        public readonly string $publishedAt,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromArticle(Article $article): self
    {
        return new self(
            id: $article->getId(),
            externalId: $article->getExternalId(),
            title: $article->getTitle(),
            description: $article->getDescription(),
            content: $article->getContent(),
            source: $article->getSourceName(),
            url: $article->getUrl(),
            imageUrl: $article->getImageUrl(),
            language: $article->getLanguage(),
            publishedAt: $article->getPublishedAt()->format('c'),
            createdAt: $article->getCreatedAt()->format('c'),
            updatedAt: $article->getUpdatedAt()->format('c'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'externalId' => $this->externalId,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'source' => $this->source,
            'url' => $this->url,
            'imageUrl' => $this->imageUrl,
            'language' => $this->language,
            'publishedAt' => $this->publishedAt,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}