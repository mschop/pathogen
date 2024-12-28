<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class ReplaceTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            'path/to/file.html.twig',
            Path::fromString('path/from/otherFile')->replace(1, ['to', 'file.html.twig'])->format('/'),
        );

        $this->assertEquals(
            'path/to/file.html.twig',
            Path::fromString('path/from/otherFile')->replace(1, ['to', 'file.html.twig', 'shouldBeIgnored'])->format('/'),
            'Replacements that are out of bound are ignored',
        );
    }
}