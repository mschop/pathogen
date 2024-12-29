<?php

namespace Pathogen\Convenience;

use Pathogen\AbsolutePath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ContainsTest extends TestCase
{
    #[DataProvider('positiveData')]
    public function testPositive(string $path, string $needle, bool $caseSensitive): void
    {
        $this->assertTrue(
            AbsolutePath::fromString($path)->contains($needle, $caseSensitive)
        );
    }

    public static function positiveData(): array
    {
        return [
            ['/etc/systemd/system/myservice', 'etc/system', true],
            ['/etc/systemd/system/myservice', 'etc\\system', true],
            ['/etc/systemd/system/myservice', 'etc/System', false],
            ['/etc/systemd/system/myservice', 'etc\\System', false],
            ['/etc/systemd/system/myservice/', 'myservice/', true],
        ];
    }

    #[DataProvider('negativeData')]
    public function testNegative(string $path, string $needle, bool $caseSensitive): void
    {
        $this->assertFalse(
            AbsolutePath::fromString($path)->contains($needle, $caseSensitive)
        );
    }

    public static function negativeData(): array
    {
        return [
             ['/etc/systemd/system/myservice', 'myservice/', true],
             ['/etc/systemd/system/myservice', 'Myservice', true],
        ];
    }
}