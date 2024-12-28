<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StripExtensionTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->stripExtension()->format('/'),
        );
    }

    public static function data(): array
    {
        return [
            ['dir/page.html.twig', 'dir/page.html'],
        ];
    }
}