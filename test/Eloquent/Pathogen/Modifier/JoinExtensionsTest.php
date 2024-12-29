<?php

namespace Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class JoinExtensionsTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            '/path/to/file.html.twig',
            Path::fromString('/path/to/file')->joinExtensions('html', 'twig'),
            'joinExtensions must extend the given extensions to the path',
        );
    }
}