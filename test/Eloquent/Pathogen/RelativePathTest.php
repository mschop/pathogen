<?php

namespace Eloquent\Pathogen;

use Mschop\Pathogen\Exception\PathTypeMismatch;
use Mschop\Pathogen\RelativePath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RelativePathTest extends TestCase
{
    #[DataProvider('commonRelativePaths')]
    public function testValidRelativePaths(string $path): void
    {
        $actual = RelativePath::fromString($path);
        $this->assertInstanceOf(RelativePath::class, $actual);
    }

    /**
     * @return string[]
     */
    public static function commonRelativePaths(): array
    {
        return [
            ['test/path'],
            ['test/path.txt'],
            ['./test/path'],
            ['./test/path.txt'],
        ];
    }

    #[DataProvider('nonRelativePaths')]
    public function testInvalidRelativePaths(string $path): void
    {
        $this->expectException(PathTypeMismatch::class);
        RelativePath::fromString($path);
    }

    public static function nonRelativePaths(): array
    {
        return [
            ['/test/path'],
            ['/test/path.txt'],
            ['/'],
        ];
    }

    public function testPathTraversal(): void
    {
        $path = '.././../test/path';
        $atoms = RelativePath::fromString($path)->atoms();
        $this->assertEquals(['..', '..', 'test', 'path'], $atoms);
    }
}