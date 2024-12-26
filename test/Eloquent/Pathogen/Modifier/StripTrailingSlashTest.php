<?php

namespace Eloquent\Pathogen\Modifier;

use Mschop\Pathogen\AbsoluteDriveAnchoredPath;
use Mschop\Pathogen\Path;
use Mschop\Pathogen\RelativePath;
use PHPUnit\Framework\TestCase;

class StripTrailingSlashTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            '/etc/systemd',
            Path::fromString('/etc/systemd/')->stripTrailingSlash()->format('/'),
            'stripTrailingSlash must remove trailing slash for path with trailing slash',
        );

        $path = RelativePath::fromString('etc/systemd');
        $this->assertSame(
            $path,
            $path->stripTrailingSlash(),
            'stripTrailingSlash should return the same object if the path does not contain a trailing slash for memory efficiency reasons',
        );
    }
}