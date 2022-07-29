<?php

namespace Eloquent\Pathogen;

use Mschop\Pathogen\AbsolutePath;
use Mschop\Pathogen\RelativePath;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function test(): void
    {
        $path = RelativePath::fromString('path\\to\\heaven');
        $this->assertEquals('path/to/heaven', $path->format('/'));

        $path = RelativePath::fromString('path\\to\\heaven\\');
        $this->assertEquals('path/to/heaven/', $path->format('/'));

        $path = AbsolutePath::fromString('\\path\\to\\heaven\\');
        $this->assertEquals('/path/to/heaven/', $path->format('/'));
    }
}