<?php

namespace Eloquent\Pathogen\Convenience;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MatchesRegexTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function test(string $path, string $regex, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->matchesRegex($regex),
        );
    }

    public static function dataProvider(): array
    {
        return [
            ['/etc/systemd/system/', '/.*system.*/', true],
            ['/etc/systemd/system/', '/.*systim.*/', false],
            ['/etc/systemd/system/', '/^\/etc\/system.*/', true],
        ];
    }
}