<?php

namespace Pathogen\Convenience;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class NameSuffixTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            'html.twig',
            Path::fromString('my-page.html.twig')->nameSuffix(),
        );

        $this->assertEquals(
            '',
            Path::fromString('myfile')->nameSuffix(),
        );
    }
}