<?php

namespace Eloquent\Pathogen\Convenience;

use Mschop\Pathogen\Path;
use PHPUnit\Framework\TestCase;

class HasExtensionTest extends TestCase
{
    public function test(): void
    {
        $this->assertTrue(Path::fromString('.htacess')->hasExtension());
        $this->assertTrue(Path::fromString('file.html.twig')->hasExtension());
        $this->assertFalse(Path::fromString('file')->hasExtension());
    }
}