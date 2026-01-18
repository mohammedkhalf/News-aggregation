<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Api;

use App\Application\Command\AsyncNewsCommand;
use App\Infrastructure\Api\GNewsHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class GNewsHttpClientTest extends TestCase
{
    public function testFetchReturnsArticles(): void
    {
        $fakeApiKey = 'test-api-key';

        $dto1 = [
            'title' => 'Article 1',
            'description' => 'Description 1',
            'content' => 'Content 1',
            'source' => ['name' => 'BBC'],
            'url' => 'https://example.com/1',
            'image' => null,
            'language' => 'en',
            'publishedAt' => '2026-01-18T10:00:00Z',
        ];

        $dto2 = [
            'title' => 'Article 2',
            'description' => 'Description 2',
            'content' => 'Content 2',
            'source' => ['name' => 'CNN'],
            'url' => 'https://example.com/2',
            'image' => 'https://example.com/image2.jpg',
            'language' => 'en',
            'publishedAt' => '2026-01-18T11:00:00Z',
        ];

        // Mock the response
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('toArray')
            ->willReturnOnConsecutiveCalls(
                ['articles' => [$dto1, $dto2]], // first page
                ['articles' => []]              // second page ends pagination
            );

        // Mock HTTP client
        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->expects($this->exactly(2))
            ->method('request')
            ->with(
                'GET',
                'https://gnews.io/api/v4/search',
                $this->callback(function ($options) {
                    // Basic structure check
                    return isset($options['query']['q']) &&
                        isset($options['query']['lang']) &&
                        isset($options['query']['token']);
                })
            )
            ->willReturn($responseMock);

        $httpClient = new GNewsHttpClient($clientMock, $fakeApiKey);

        $command = new AsyncNewsCommand(
            keyword: 'technology',
            language: 'en',
            fromDate: '2026-01-01',
            toDate: '2026-01-18'
        );

        $articles = iterator_to_array($httpClient->fetch($command));

        $this->assertCount(2, $articles);

        $this->assertSame('https://example.com/1', $articles[0]['externalId']);
        $this->assertSame('Article 1', $articles[0]['title']);
        $this->assertSame('BBC', $articles[0]['source']);

        $this->assertSame('https://example.com/2', $articles[1]['externalId']);
        $this->assertSame('CNN', $articles[1]['source']);
        $this->assertSame('https://example.com/image2.jpg', $articles[1]['imageUrl']);
    }

    public function testFetchYieldsEmptyWhenNoArticles(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('toArray')
            ->willReturn(['articles' => []]);

        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->method('request')->willReturn($responseMock);

        $httpClient = new GNewsHttpClient($clientMock, 'key');

        $command = new AsyncNewsCommand(
            keyword: 'empty',
            language: 'en',
            fromDate: '2026-01-01',
            toDate: '2026-01-18'
        );

        $articles = iterator_to_array($httpClient->fetch($command));

        $this->assertSame([], $articles);
    }
}
