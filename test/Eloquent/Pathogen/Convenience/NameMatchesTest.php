<?php

namespace Eloquent\Pathogen\Convenience;

use Mschop\Pathogen\Exception\InvalidArgumentException;
use Mschop\Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NameMatchesTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $needle, bool $caseSensitive, int $flags, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->nameMatches($needle, $caseSensitive, $flags),
        );
    }

    public static function data(): array
    {
        return [
            ['file.txt', 'file*', true, 0, true],
            ['file.txt', 'File*', false, 0, true],
            ['file.txt', 'File*', true, 0, false],
        ];
    }

    public function testDisallowedFlag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Path::fromString('test.txt')->nameMatches('test', true, FNM_NOESCAPE | FNM_CASEFOLD);
    }
}