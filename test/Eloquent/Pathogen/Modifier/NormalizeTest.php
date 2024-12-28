<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NormalizeTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $normalized): void
    {
        $this->assertEquals(
            $normalized,
            Path::fromString($path)->normalize()->format('/'),
        );
    }

    public static function data(): array
    {
        return [
            ['/foo/../bar/./', '/bar/'],
            ['../../foo/./bar', '../../foo/bar'],
        ];
    }
}