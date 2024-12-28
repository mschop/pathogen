<?php

namespace Eloquent\Pathogen\Convenience;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NameContainsTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $needle, bool $caseSensitive, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->nameContains($needle, $caseSensitive),
        );
    }

    public static function data(): array
    {
        return [
            ['/path/to/file.php', '.php', true, true],
            ['/path/to/file.php', '.Php', true, false],
            ['/path/to/file.php', 'file', true, true],
            ['/path/to/file.php', '0', true, false],
        ];
    }
}