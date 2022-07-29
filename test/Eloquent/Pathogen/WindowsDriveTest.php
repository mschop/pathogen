<?php

namespace Eloquent\Pathogen;

use Mschop\Pathogen\AbsoluteDriveAnchoredPath;
use Mschop\Pathogen\RelativeDriveAnchoredPath;
use Mschop\Pathogen\RelativePath;
use PHPUnit\Framework\TestCase;

class WindowsDriveTest extends TestCase
{
    public function testWindowsWindowsDriveInterpretedAsRelativeAtom(): void
    {
        $path = 'C:\\test\\drive';
        $path = RelativePath::fromString($path);
        $this->assertEquals(
            ['C:', 'test', 'drive'],
            $path->atoms(),
            'If the windows drive shall not be interpreted, the atoms should be parsed "as is"',
        );
    }

    public function testAbsoluteWindowsPath(): void
    {
        $path = 'C:\\test\\drive';
        $path = AbsoluteDriveAnchoredPath::fromString($path);
        $this->assertEquals('C', $path->getDrive());
    }

    public function testRelativeWindowsPath(): void
    {
        $path = 'C:test\\drive';
        $path = RelativeDriveAnchoredPath::fromString($path);
        $this->assertInstanceOf(RelativeDriveAnchoredPath::class, $path);
        $this->assertEquals('C', $path->getDrive());
        $this->assertEquals(['test', 'drive'], $path->atoms());
    }
}