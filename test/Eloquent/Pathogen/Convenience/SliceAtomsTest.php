<?php

namespace Eloquent\Pathogen\Convenience;

use Pathogen\Exception\InvalidArgumentException;
use Pathogen\RelativePath;
use PHPUnit\Framework\TestCase;

class SliceAtomsTest extends TestCase
{
    public function test()
    {
        $path = RelativePath::fromString('veni/vidi/vici/adoramus/te');
        $slice = $path->sliceAtoms(1, 2);
        $this->assertEquals(['vidi', 'vici'], $slice);
    }

    public function testNegativeIndex()
    {
        $path = RelativePath::fromString('veni/vidi/vici/adoramus/te');
        $slice = $path->sliceAtoms(-3, 2);
        $this->assertEquals(['vici', 'adoramus'], $slice);
    }

    public function testNegativeLength()
    {
        $path = RelativePath::fromString('veni/vidi/vici/adoramus/te');
        $slice = $path->sliceAtoms(2, -1);
        $this->assertEquals(['vici', 'adoramus'], $slice);
    }
}