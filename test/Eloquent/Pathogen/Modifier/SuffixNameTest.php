<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class SuffixNameTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            'path/to/files.html.twig',
            Path::fromString('path/to/file')->suffixName('s.html.twig'),
        );
    }
}