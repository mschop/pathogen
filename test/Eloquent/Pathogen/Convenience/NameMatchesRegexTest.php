<?php

namespace Eloquent\Pathogen\Convenience;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NameMatchesRegexTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $regex, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->nameMatchesRegex($regex),
        );
    }

    public static function data(): array
    {
        return [
            ['/path/to/file.txt', '/.*to.*txt/', false],
            ['/path/to/file.txt', '/.*txt/', true],
        ];
    }
}