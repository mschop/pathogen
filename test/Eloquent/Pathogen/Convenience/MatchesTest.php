<?php

namespace Eloquent\Pathogen\Convenience;

use Mschop\Pathogen\AbsoluteDriveAnchoredPath;
use Mschop\Pathogen\Exception\InvalidArgumentException;
use Mschop\Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MatchesTest extends TestCase
{
    #[DataProvider('data_testPositive')]
    public function testPositive(string $path, string $pattern, bool $caseSensitive, int $flags): void
    {
        $this->assertTrue(
            Path::fromString($path)->matches($pattern, $caseSensitive, $flags),
        );
    }

    public static function data_testPositive(): array
    {
        return [
            ['/etc/hosts', '/etc*', true, 0],
            ['/etc/hosts', '*tc*', true, 0],
            ['/etc/systemd/system/my.service', '/etc/**/*/my.service', true, 0],
            ['/etc/hosts', '/Etc*', false, 0],
        ];
    }

    #[DataProvider('data_testNegative')]
    public function testNegative(string $path, string $pattern, bool $caseSensitive, int $flags): void
    {
        $this->assertFalse(
            Path::fromString($path)->matches($pattern, $caseSensitive, $flags),
        );
    }

    public static function data_testNegative(): array
    {
        return [
            ['/etc/hosts', '/etec/*', true, 0],
            ['/etc/hosts', '/Etc/*', true, 0],
            ['/etc/hosts', '/etc/', true, 0],
        ];
    }

    public function testWindows(): void
    {
        $this->assertTrue(
            AbsoluteDriveAnchoredPath::fromString('C:\\path\\to\\hell')->matches('C:\\*')
        );

        $this->assertFalse(
            AbsoluteDriveAnchoredPath::fromString('C:\\path\\to\\hell')->matches('\\path')
        );
    }

    public function testDisallowedFlag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Path::fromString('test')->matches('test', true, FNM_CASEFOLD | FNM_NOESCAPE);
    }
}