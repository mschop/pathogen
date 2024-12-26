<?php

namespace Eloquent\Pathogen\Modifier;

use Mschop\Pathogen\AbsolutePath;
use Mschop\Pathogen\Exception\InvalidPathStateException;
use Mschop\Pathogen\Path;
use Mschop\Pathogen\RelativeDriveAnchoredPath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParentTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, int $levels, string $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->parent($levels)->format('/'),
        );
    }

    public static function data(): array
    {
        return [
            ['/test/path', 1, '/test'],
            ['/test/path/', 1, '/test'],
            ['/test/path/', 2, '/'],
            ['../test/path/', 4, '..'],
            ['../test/path/', 5, '../..'],
        ];
    }

    public function testPathTraversalOverRoot(): void
    {
        $this->expectException(InvalidPathStateException::class);
        AbsolutePath::fromString('/path/to')->parent(3);
    }

    public function testWindows(): void
    {
        $this->assertEquals(
            'C:path',
            RelativeDriveAnchoredPath::fromString('C:path/to')->parent(1)->format('/'),
        );

        $this->assertEquals(
            'C:',
            RelativeDriveAnchoredPath::fromString('C:path/to')->parent(2)->format('/'),
        );

        $this->assertEquals(
            'C:..',
            RelativeDriveAnchoredPath::fromString('C:path/to')->parent(3)->format('/'),
        );
    }
}