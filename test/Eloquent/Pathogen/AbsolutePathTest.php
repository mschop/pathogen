<?php

namespace Pathogen;

use Pathogen\AbsolutePath;
use Pathogen\Exception\InvalidPathStateException;
use Pathogen\Exception\PathTypeMismatchException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AbsolutePathTest extends TestCase
{
    #[DataProvider('validAbsolutePaths')]
    public function testValidAbsolutePaths(string $path): void
    {
        $path = AbsolutePath::fromString($path);
        $this->assertInstanceOf(AbsolutePath::class, $path);
    }

    public static function validAbsolutePaths(): array
    {
        return [
            ['/root/filesystem-blub/file.txt'],
            ['/file.txt'],
            ['/directory stuff/file is-here.txt'],
        ];
    }

    #[DataProvider('invalidAbsolutePaths')]
    public function testInvalidAbsolutePaths(string $path): void
    {
        $this->expectException(PathTypeMismatchException::class);
        AbsolutePath::fromString($path);
    }

    public static function invalidAbsolutePaths(): array
    {
        return [
            ['root/filesystem-blub/file.txt'],
            ['./root/filesystem-blub/file.txt'],
            ['../root/filesystem-blub/file.txt'],
        ];
    }

    public function testOutOfBound(): void
    {
        $this->expectException(InvalidPathStateException::class);
        new AbsolutePath(atoms: ['test', '..', '..', 'file.txt'], hasTrailingSeparator: true);
    }
}