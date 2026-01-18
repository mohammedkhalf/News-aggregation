<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Article\ValueObject;

use App\Domain\Article\ValueObject\Source;
use PHPUnit\Framework\TestCase;

final class SourceTest extends TestCase
{
    public function testCanInstantiateSource(): void
    {
        $source = new Source('BBC');

        $this->assertInstanceOf(Source::class, $source);
        $this->assertSame('BBC', (string) $source);
    }

    public function testDifferentSourceNames(): void
    {
        $names = ['CNN', 'Reuters', 'Al Jazeera', 'The Guardian'];

        foreach ($names as $name) {
            $source = new Source($name);
            $this->assertSame($name, (string)$source);
        }
    }

    public function testEmptySourceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Source('');
    }
}
