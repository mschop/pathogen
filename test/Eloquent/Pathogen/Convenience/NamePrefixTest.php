<?php

namespace Pathogen\Convenience;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class NamePrefixTest extends TestCase
{
    public function test()
    {
        $this->assertEquals(
            'page',
            Path::fromString('page.html.twig')->namePrefix(),
        );

        $this->assertEquals(
            '',
            Path::fromString('.htaccess')->namePrefix(),
        );
    }
}