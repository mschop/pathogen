<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StripNameSuffixTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->stripNameSuffix()->format('/'),
        );
    }

    public static function data(): array
    {
        return [
            ['test/myfile.html.twig', 'test/myfile'],
        ];
    }
}