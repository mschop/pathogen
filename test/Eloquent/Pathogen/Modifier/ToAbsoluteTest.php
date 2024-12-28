<?php

namespace Eloquent\Pathogen\Modifier;

use Pathogen\RelativeDriveAnchoredPath;
use Pathogen\RelativePath;
use PHPUnit\Framework\TestCase;

class ToAbsoluteTest extends TestCase
{
    public function test(): void
    {
        $relativePath = RelativePath::fromString('./etc/systemd/system/');
        $absolutePath = $relativePath->toAbsolute();
        $this->assertEquals('/etc/systemd/system/', $absolutePath->format('/'));
    }

    public function testDriveAnchored(): void
    {
        $relativePath = RelativeDriveAnchoredPath::fromString('D:etc\\systemd\\system');
        $absolutePath = $relativePath->toAbsolute();
        $this->assertEquals('D:/etc/systemd/system', $absolutePath->format('/'));
    }
}