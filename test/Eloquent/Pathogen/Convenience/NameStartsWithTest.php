<?php

namespace Pathogen\Convenience;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NameStartsWithTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $needle, bool $caseSensitive, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->nameStartsWith($needle, $caseSensitive),
        );
    }

    public static function data(): array
    {
        return [
            ['test.txt', 'test', true, true],
            ['test.txt', 'Test', true, false],
            ['test.txt', 'Test', false, true],
        ];
    }
}