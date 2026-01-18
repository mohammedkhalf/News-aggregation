<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Article;

use App\Domain\Article\Article;
use App\Domain\Article\Article\ValueObject\Language;
use App\Domain\Article\Article\ValueObject\Source;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ArticleTest extends TestCase
{
    private array $validDto;

    protected function setUp(): void
    {
        $this->validDto = [
            'externalId' => 'ext-123',
            'title' => 'Sample Article',
            'description' => 'This is a description',
            'content' => 'This is the content',
            'source' => 'BBC',
            'url' => 'https://example.com/article',
            'imageUrl' => 'https://example.com/image.jpg',
            'language' => 'en',
            'publishedAt' => '2026-01-18T12:00:00+00:00',
        ];
    }

    public function testCreateFromDto(): void
    {
        $article = Article::createFromDto($this->validDto);

        $this->assertSame($this->validDto['externalId'], $article->getExternalId());
        $this->assertSame($this->validDto['title'], $article->getTitle());
        $this->assertSame($this->validDto['description'], $article->getDescription());
        $this->assertSame($this->validDto['content'], $article->getContent());
        $this->assertSame($this->validDto['source'], $article->getSourceName());
        $this->assertSame($this->validDto['url'], $article->getUrl());
        $this->assertSame($this->validDto['imageUrl'], $article->getImageUrl());
        $this->assertSame($this->validDto['language'], $article->getLanguage());
        $this->assertInstanceOf(DateTimeImmutable::class, $article->getPublishedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $article->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $article->getUpdatedAt());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $article->getId()
        );
    }

    public function testUpdateContent(): void
    {
        $article = Article::createFromDto($this->validDto);

        $updateDto = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'content' => 'Updated content',
        ];

        $originalUpdatedAt = $article->getUpdatedAt();

        sleep(1); // Ensure updatedAt changes

        $article->updateContent($updateDto);

        $this->assertSame($updateDto['title'], $article->getTitle());
        $this->assertSame($updateDto['description'], $article->getDescription());
        $this->assertSame($updateDto['content'], $article->getContent());
        $this->assertNotEquals($originalUpdatedAt, $article->getUpdatedAt());
    }

    public function testCreateFromDtoWithInvalidSourceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $invalidDto = $this->validDto;
        $invalidDto['source'] = ''; // Invalid Source

        Article::createFromDto($invalidDto);
    }

    public function testCreateFromDtoWithInvalidLanguageThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $invalidDto = $this->validDto;
        $invalidDto['language'] = 'xx'; // Invalid Language

        Article::createFromDto($invalidDto);
    }

    public function testOptionalFieldsCanBeNull(): void
    {
        $dto = $this->validDto;
        $dto['description'] = null;
        $dto['content'] = null;
        $dto['imageUrl'] = null;

        $article = Article::createFromDto($dto);

        $this->assertNull($article->getDescription());
        $this->assertNull($article->getContent());
        $this->assertNull($article->getImageUrl());
    }
}
