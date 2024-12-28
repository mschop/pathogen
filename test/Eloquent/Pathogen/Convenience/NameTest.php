<?php

namespace Eloquent\Pathogen\Convenience;

use Pathogen\AbsolutePath;
use Pathogen\RelativePath;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function test()
    {
        $this->assertEquals(
            'file.txt',
            RelativePath::fromString('path/to/file.txt')->name(),
        );

        $this->assertEquals(
            'file',
            RelativePath::fromString('path/to/file')->name(),
        );

        $this->assertEquals(
            'file.txt',
            RelativePath::fromString('path/to/file.txt/')->name(),
        );

        $this->assertEquals(
            '',
            AbsolutePath::fromString('/')->name(),
        );
    }
}