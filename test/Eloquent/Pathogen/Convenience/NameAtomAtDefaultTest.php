<?php

namespace Eloquent\Pathogen\Convenience;

use Pathogen\Path;
use Pathogen\RelativePath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NameAtomAtDefaultTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, int $offset, string $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->nameAtomAtDefault($offset),
        );
    }

    public static function data(): array
    {
        return [
            ['file.txt', 1, 'txt'],
            ['file.txt', -1, 'txt'],
            ['file.txt', -2, 'file'],
        ];
    }

    public function testDefaults(): void
    {
        $this->assertEquals(
            'fallback',
            Path::fromString('file.txt')->nameAtomAtDefault(2, 'fallback'),
        );

        $this->assertEquals(
            'fallback',
            Path::fromString('file.txt')->nameAtomAtDefault(-3, 'fallback'),
        );
    }
}