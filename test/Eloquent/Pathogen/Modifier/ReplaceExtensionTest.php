<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ReplaceExtensionTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $replace, string $expected): void
    {
        $this->assertEquals(
            $expected,
            Path::fromString($path)->replaceExtension($replace)->format('/'),
        );
    }

    public static function data(): array
    {
        return [
            ['/path/to/file.html.twig', 'php', '/path/to/file.html.php'],
            ['/path/to/file.html.twig', 'blade.php', '/path/to/file.html.blade.php'],
            ['/path/to/.twig', '.blade.php', '/path/to/.blade.php'],
            ['/path/to/file', '.html.twig', '/path/to/file.html.twig'],
        ];
    }
}