<?php

namespace Pathogen\Convenience;

use Pathogen\AbsoluteDriveAnchoredPath;
use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EndsWithTest extends TestCase
{
    #[DataProvider('data_testPositive')]
    public function testPositive(string $path, string $needle, bool $caseSensitive): void
    {
        $this->assertTrue(
            Path::fromString($path)->endsWith($needle, $caseSensitive)
        );
    }

    public static function data_testPositive(): array
    {
        return [
            ['/path/to', '/to', true],
            ['/path/to/', '/to/', true],
            ['/path/to', '/To', false],
            ['path/to', 'to', true],
            ['path/to', 'To', false],
        ];
    }

    #[DataProvider('data_testNegative')]
    public function testNegative(string $path, string $needle, bool $caseSensitive): void
    {
        $this->assertFalse(
            Path::fromString($path)->endsWith($needle, $caseSensitive)
        );
    }

    public static function data_testNegative(): array
    {
        return [
            ['/path/to', '/To', true],
            ['/path/to/', 'to', true],
            ['/paTh/to', 'To', true],
        ];
    }

    #[DataProvider('data_testWindows')]
    public function testWindows(string $path, string $needle): void
    {
        $this->assertTrue(
            AbsoluteDriveAnchoredPath::fromString($path)->endsWith($needle),
        );
    }

    public static function data_testWindows():array
    {
        return [
            ['C:\\test\\test', '\\test'],
        ];
    }
}