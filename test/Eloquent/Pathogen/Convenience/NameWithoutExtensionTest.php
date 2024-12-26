<?php

namespace Eloquent\Pathogen\Convenience;

use Mschop\Pathogen\Path;
use PHPUnit\Framework\TestCase;

class NameWithoutExtensionTest extends TestCase
{
    public function test(): void
    {
        $this->assertEquals(
            'this.is.my.file',
            Path::fromString('this.is.my.file.txt')->nameWithoutExtension(),
        );

        $this->assertEquals(
            '',
            Path::fromString('.htaccess')->nameWithoutExtension(),
        );

        $this->assertEquals(
            'filename',
            Path::fromString('filename')->nameWithoutExtension(),
        );
    }
}