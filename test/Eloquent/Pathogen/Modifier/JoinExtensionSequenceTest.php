<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class JoinExtensionSequenceTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            'path/to/file.html.twig',
            Path::fromString('path/to/file')->joinExtensionSequence(['html', 'twig'])->format('/')
        );
    }
}