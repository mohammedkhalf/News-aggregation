<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Article\ValueObject;

use App\Domain\Article\ValueObject\Language;
use PHPUnit\Framework\TestCase;

final class LanguageTest extends TestCase
{
    public function testCanInstantiateLanguage(): void
    {
        $lang = new Language('en');

        $this->assertInstanceOf(Language::class, $lang);
        $this->assertSame('en', (string)$lang);
    }

    public function testDifferentLanguageCodes(): void
    {
        $codes = ['en', 'fr', 'de', 'es', 'ar'];

        foreach ($codes as $code) {
            $lang = new Language($code);
            $this->assertSame($code, (string)$lang);
        }
    }
}
