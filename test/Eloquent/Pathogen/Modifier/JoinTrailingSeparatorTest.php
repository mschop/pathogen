<?php

namespace Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\TestCase;

class JoinTrailingSeparatorTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            '/path/to/this/',
            Path::fromString('/path/to/this')->joinTrailingSeparator()->format('/'),
            'joinTrailingSlash must add a trailing slash for a path without trailing slash',
        );

        $pathWithTrailingSlash = Path::fromString('/path/');
        $this->assertSame(
            $pathWithTrailingSlash,
            $pathWithTrailingSlash->joinTrailingSeparator(),
            'joinTrailingSlash should return the same object for paths, that already have a trailing slash for memory efficiency reasons',
        );
    }
}