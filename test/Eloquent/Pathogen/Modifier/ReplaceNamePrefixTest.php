<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ReplaceNamePrefixTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $replace, string $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->replaceNamePrefix($replace)->format('/'),
        );
    }

    public static function data(): array
    {
        return [
            ['/path/to/file.html.twig', 'other', '/path/to/other.html.twig'],
            ['/path/to/.html.twig', 'other', '/path/to/other.html.twig'],
        ];
    }
}