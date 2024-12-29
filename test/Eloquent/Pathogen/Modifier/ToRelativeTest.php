<?php

namespace Pathogen\Modifier;

use Pathogen\AbsoluteDriveAnchoredPath;
use Pathogen\AbsolutePath;
use PHPUnit\Framework\TestCase;

class ToRelativeTest extends TestCase
{
    public function test(): void
    {
        $absolutePath = AbsolutePath::fromString('/etc/systemd/system');
        $relativePath = $absolutePath->toRelative();

        $this->assertEquals('etc/systemd/system', $relativePath->format('/'));
    }

    public function testDriveAnchored(): void
    {
        $absolutePath = AbsoluteDriveAnchoredPath::fromString('C:/etc/systemd/system');
        $relativePath = $absolutePath->toRelative();
        $this->assertEquals('C:etc/systemd/system', $relativePath->format('/'));
        $this->assertEquals('C:etc\\systemd\\system', $relativePath->format('\\'));
    }
}