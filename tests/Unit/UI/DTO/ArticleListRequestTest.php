<?php

namespace App\Tests\Unit\UI\DTO;

use App\UI\DTO\ArticleListRequest;
use PHPUnit\Framework\TestCase;

class ArticleListRequestTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $request = new ArticleListRequest();

        $this->assertSame(1, $request->page);
        $this->assertSame(20, $request->limit);
        $this->assertNull($request->language);
        $this->assertNull($request->source);
        $this->assertSame('publishedAt', $request->sortBy);
        $this->assertSame('DESC', $request->sortOrder);
        $this->assertSame(0, $request->getOffset());
    }

    public function testFromRequestWithValidParams(): void
    {
        $queryParams = [
            'page' => '2',
            'limit' => '15',
            'language' => 'en',
            'source' => 'BBC',
            'sortBy' => 'title',
            'sortOrder' => 'ASC',
        ];

        $request = ArticleListRequest::fromRequest($queryParams);

        $this->assertSame(2, $request->page);
        $this->assertSame(15, $request->limit);
        $this->assertSame('en', $request->language);
        $this->assertSame('BBC', $request->source);
        $this->assertSame('title', $request->sortBy);
        $this->assertSame('ASC', $request->sortOrder);
        $this->assertSame(15, $request->getOffset());
    }

    public function testFromRequestWithInvalidOrMissingParams(): void
    {
        $queryParams = [
            'page' => '-5',
            'limit' => '200', // exceeds max 100
            'language' => 123, // not string
            'source' => true,  // not string
            'sortOrder' => 'random', // invalid
        ];

        $request = ArticleListRequest::fromRequest($queryParams);

        $this->assertSame(1, $request->page);          // min 1
        $this->assertSame(100, $request->limit);       // capped at 100
        $this->assertNull($request->language);         // invalid
        $this->assertNull($request->source);           // invalid
        $this->assertSame('publishedAt', $request->sortBy); // default
        $this->assertSame('DESC', $request->sortOrder);     // default
        $this->assertSame(0, $request->getOffset());
    }

    public function testOffsetCalculation(): void
    {
        $request = new ArticleListRequest(page: 3, limit: 25);
        $this->assertSame(50, $request->getOffset());
    }

}
