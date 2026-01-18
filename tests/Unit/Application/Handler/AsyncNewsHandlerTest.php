<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler;

use App\Application\Command\AsyncNewsCommand;
use App\Application\Handler\AsyncNewsHandler;
use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Infrastructure\Api\GNewsHttpClient;
use PHPUnit\Framework\TestCase;

final class AsyncNewsHandlerTest extends TestCase
{
    public function testInvokeInsertsNewArticles(): void
    {
        $dto = [
            'externalId' => 'ext-123',
            'title' => 'New Article',
            'description' => 'Description here',
            'content' => 'Content here',
            'source' => 'BBC',
            'url' => 'https://example.com/article',
            'imageUrl' => null,
            'language' => 'en',
            'publishedAt' => '2026-01-18T10:00:00+00:00'
        ];

        // Mock GNewsHttpClient
        $clientMock = $this->createMock(GNewsHttpClient::class);
        $clientMock->method('fetch')
            ->willReturn([$dto]);

        // Mock ArticleRepositoryInterface
        $repositoryMock = $this->createMock(ArticleRepositoryInterface::class);
        // findByExternalId returns null → new article
        $repositoryMock->method('findByExternalId')
            ->with($dto['externalId'])
            ->willReturn(null);

        // Expect upsert to be called once with an Article object
        $repositoryMock->expects($this->once())
            ->method('upsert')
            ->with($this->callback(fn($article) => $article instanceof Article && $article->getExternalId() === 'ext-123'));

        $handler = new AsyncNewsHandler($clientMock, $repositoryMock);

        $command = $this->createMock(AsyncNewsCommand::class);

        $handler->__invoke($command);
    }

    public function testInvokeUpdatesExistingArticle(): void
    {
        $dto = [
            'externalId' => 'ext-456',
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'content' => 'Updated Content',
            'source' => 'CNN',
            'url' => 'https://example.com/article2',
            'imageUrl' => null,
            'language' => 'en',
            'publishedAt' => '2026-01-18T11:00:00+00:00'
        ];

        $existingArticle = Article::createFromDto($dto);

        // Mock GNewsHttpClient
        $clientMock = $this->createMock(GNewsHttpClient::class);
        $clientMock->method('fetch')
            ->willReturn([$dto]);

        // Mock repository to return existing article
        $repositoryMock = $this->createMock(ArticleRepositoryInterface::class);
        $repositoryMock->method('findByExternalId')
            ->with($dto['externalId'])
            ->willReturn($existingArticle);

        // Expect upsert called to update the article
        $repositoryMock->expects($this->once())
            ->method('upsert')
            ->with($this->callback(fn($article) => $article === $existingArticle));

        $handler = new AsyncNewsHandler($clientMock, $repositoryMock);

        $command = $this->createMock(AsyncNewsCommand::class);

        $handler->__invoke($command);

        // Assert the article has updated title/content
        $this->assertSame('Updated Title', $existingArticle->getTitle());
        $this->assertSame('Updated Description', $existingArticle->getDescription());
        $this->assertSame('Updated Content', $existingArticle->getContent());
    }
}
