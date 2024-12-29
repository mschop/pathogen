<?php

namespace Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ReplaceNameSuffixTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $replace, string $expected): void
    {
        $this->assertEquals(
            $expected,
            Path::fromString($path)->replaceNameSuffix($replace)->format('/'),
        );
    }

    public static function data(): array
    {
        return [
            ['/path/to/file.html.twig', 'blade.php', '/path/to/file.blade.php'],
            ['/path/to/.html.twig', 'blade.php', '/path/to/.blade.php'],
            ['/path/to/.html.twig', '.blade.php', '/path/to/.blade.php'],
            ['/path/to/.twig', '.blade.php', '/path/to/.blade.php'],
            ['/path/to/twig', '.blade.php', '/path/to/twig.blade.php'],
        ];
    }
}