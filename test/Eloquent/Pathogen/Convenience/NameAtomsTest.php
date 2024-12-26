<?php

namespace Eloquent\Pathogen\Convenience;

use Mschop\Pathogen\AbsoluteDriveAnchoredPath;
use Mschop\Pathogen\RelativePath;
use PHPUnit\Framework\TestCase;

class NameAtomsTest extends TestCase
{
    public function test()
    {
        $this->assertEquals(
            ['test', 'extension'],
            AbsoluteDriveAnchoredPath::fromString('C:\\directory\\structure\\test.extension')->nameAtoms(),
        );

        $this->assertEquals(
            ['extension'],
            AbsoluteDriveAnchoredPath::fromString('C:\\directory\\structure\\.extension')->nameAtoms(),
        );

        $this->assertEquals(
            ['file'],
            AbsoluteDriveAnchoredPath::fromString('C:\\directory\\structure\\file')->nameAtoms(),
        );
    }
}