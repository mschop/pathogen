<?php

namespace Pathogen\Convenience;

use Pathogen\AbsolutePath;
use Pathogen\Exception\UndefinedAtomException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AtomAtTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, int $index, string $expected): void
    {
        $this->assertEquals(
            $expected,
            AbsolutePath::fromString($path)->atomAt($index),
        );
    }

    public static function data(): array
    {
        return [
            ['/my/path/is/dirty', 2, 'is'],
            ['/my/path/is/dirty', 2, 'is'],
            ['/my/path/is/dirty.com', 3, 'dirty.com'],
            ['/my/path/is/dirty.com', -1, 'dirty.com'],
            ['/my/path/is/dirty.com', -2, 'is'],
        ];
    }

    public function testInvalidPositiveIndex(): void
    {
        $this->expectException(UndefinedAtomException::class);
        AbsolutePath::fromString('/das/ist/das/haus/vom/nikolaus')->atomAt(6);
    }

    public function testInvalidNegativeIndex(): void
    {
        $this->expectException(UndefinedAtomException::class);
        AbsolutePath::fromString('/das/ist/das/haus/vom/nikolaus')->atomAt(-7);
    }
}