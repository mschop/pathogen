<?php

namespace Pathogen\Modifier;

use Pathogen\Path;
use Pathogen\RelativePath;
use PHPUnit\Framework\TestCase;

class JoinTest extends TestCase
{
    public function test()
    {
        $this->assertEquals(
            '/path/to/some.file',
            Path::fromString('/path')->join(RelativePath::fromString('to/some.file'))->format('/'),
        );
    }
}