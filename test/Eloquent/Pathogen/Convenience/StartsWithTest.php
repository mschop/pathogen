<?php

namespace Eloquent\Pathogen\Convenience;

use Mschop\Pathogen\AbsoluteDriveAnchoredPath;
use Mschop\Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StartsWithTest extends TestCase
{
    #[DataProvider('data_testPositive')]
    public function testPositive(string $path, string $needle, bool $caseSensitive): void
    {
        $this->assertTrue(
            Path::fromString($path)->startsWith($needle, $caseSensitive)
        );
    }

    public static function data_testPositive(): array
    {
        return [
            ['/path/to', '/path', true],
            ['/path/to', '/Path', false],
            ['path/to', 'path', true],
            ['path/to', 'Path', false],
        ];
    }

    #[DataProvider('data_testNegative')]
    public function testNegative(string $path, string $needle, bool $caseSensitive): void
    {
        $this->assertFalse(
            Path::fromString($path)->startsWith($needle, $caseSensitive)
        );
    }

    public static function data_testNegative(): array
    {
        return [
            ['/path/to', 'path', true],
            ['/path/to', '/Path', true],
            ['/paTh/to', '/path', true],
        ];
    }

    #[DataProvider('data_testWindows')]
    public function testWindows(string $path, string $needle): void
    {
        $this->assertTrue(
            AbsoluteDriveAnchoredPath::fromString($path)->startsWith($needle),
        );
    }

    public static function data_testWindows():array
    {
        return [
            ['C:\\test\\test', 'C:\\te'],
            ['C:\\test\\test', 'C:/te'],
        ];
    }
}