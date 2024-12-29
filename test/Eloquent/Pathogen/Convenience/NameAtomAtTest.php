<?php

namespace Pathogen\Convenience;

use Pathogen\AbsolutePath;
use Pathogen\Exception\UndefinedAtomException;
use PHPUnit\Framework\TestCase;

class NameAtomAtTest extends TestCase
{
    public function test()
    {
        $this->assertEquals(
            'txt',
            AbsolutePath::fromString('/root/test.txt')->nameAtomAt(1),
        );

        $this->assertEquals(
            'txt',
            AbsolutePath::fromString('/root/test.txt')->nameAtomAt(1),
        );

        $this->assertEquals(
            'fun',
            AbsolutePath::fromString('/root/test.fun.txt')->nameAtomAt(-2),
        );
    }

    public function testInvalidPositiveIndex(): void
    {
        $this->expectException(UndefinedAtomException::class);
        AbsolutePath::fromString('/test.fun.txt')->nameAtomAt('3');
    }

    public function testInvalidNegativeIndex(): void
    {
        $this->expectException(UndefinedAtomException::class);
        AbsolutePath::fromString('/test.fun.txt')->nameAtomAt(-4);
    }
}