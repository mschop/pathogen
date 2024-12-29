<?php

namespace Pathogen\Convenience;

use Pathogen\AbsolutePath;
use PHPUnit\Framework\TestCase;

class HasAtomsTest extends TestCase
{
    public function test()
    {
        $this->assertFalse(AbsolutePath::fromString('/')->hasAtoms());
        $this->assertTrue(AbsolutePath::fromString('/ihave')->hasAtoms());
    }
}