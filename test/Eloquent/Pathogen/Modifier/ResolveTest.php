<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ResolveTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, string $resolvePath, string $result): void
    {
        $this->assertEquals(
            $result,
            Path::fromString($path)->resolve(Path::fromString($resolvePath))->format('/')
        );
    }

    public static function data(): array
    {
        return [
            ['/foo/bar', 'fizz/buzz', '/foo/bar/fizz/buzz'],
            ['foo/bar', 'fizz/buzz', 'foo/bar/fizz/buzz'],
            ['/foo/bar', '/fizz/buzz', '/fizz/buzz'],
            ['foo/bar', '/fizz/buzz', '/fizz/buzz'],
            ['/foo/bar', '../fizz/buzz', '/foo/fizz/buzz'],
            ['/foo/bar', '../fizz/buzz', '/foo/fizz/buzz'],
            ['/foo/bar', 'fizz/buzz/', '/foo/bar/fizz/buzz/']
        ];
    }
}