<?php

namespace Eloquent\Pathogen\Modifier;

use Mschop\Pathogen\Path;
use PHPUnit\Framework\TestCase;

class ReplaceNameWithoutExtensionTest extends TestCase
{
    public function test(): void
    {
        $path = '/path/to/file.html.twig';
        $path = Path::fromString($path);
        $this->assertEquals('/path/to/something.else.twig', $path->replaceNameWithoutExtension('something.else'));
    }
}