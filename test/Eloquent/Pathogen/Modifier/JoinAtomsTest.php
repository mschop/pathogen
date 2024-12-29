<?php

namespace Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class JoinAtomsTest extends TestCase
{
    public function test()
    {
        $this->assertEquals(
            'path/to/file.txt',
            Path::fromString('path')->joinAtoms('to', 'file.txt')->format('/')
        );
    }
}