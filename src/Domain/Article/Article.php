<?php

namespace App\Domain\Article;

use App\Domain\Article\ValueObject\Language;
use App\Domain\Article\ValueObject\Source;
use DateTimeImmutable;

class Article
{
    private string $externalId;
    private string $title;
    private ?string $description;
    private ?string $content;
    private Source $source;
    private string $url;
    private ?string $imageUrl;
    private Language $language;
    private DateTimeImmutable $publishedAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        string $externalId,
        string $title,
        ?string $description,
        ?string $content,
        Source $source,
        string $url,
        ?string $imageUrl,
        Language $language,
        DateTimeImmutable $publishedAt
    ) {
        $this->externalId = $externalId;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->source = $source;
        $this->url = $url;
        $this->imageUrl = $imageUrl;
        $this->language = $language;
        $this->publishedAt = $publishedAt;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function createFromDto(array $dto): self
    {
        return new self(
            $dto['externalId'],
            $dto['title'],
            $dto['description'] ?? null,
            $dto['content'] ?? null,
            new Source($dto['source']),
            $dto['url'],
            $dto['imageUrl'] ?? null,
            new Language($dto['language']),
            new DateTimeImmutable($dto['publishedAt'])
        );
    }

    public function updateContent(array $dto): void
    {
        $this->title = $dto['title'];
        $this->description = $dto['description'] ?? null;
        $this->content = $dto['content'] ?? null;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

}