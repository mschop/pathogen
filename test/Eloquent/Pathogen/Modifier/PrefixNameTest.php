<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class PrefixNameTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            'path/to/this.awesome.file',
            Path::fromString('path/to/file')->prefixName('this.awesome.')->format('/'),
        );
    }
}