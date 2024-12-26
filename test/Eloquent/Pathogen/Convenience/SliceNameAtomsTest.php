<?php

namespace Eloquent\Pathogen\Convenience;

use Mschop\Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SliceNameAtomsTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, int $index, int|null $length, array $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            Path::fromString($path)->sliceNameAtoms($index, $length),
        );
    }

    public static function data(): array
    {
        return [
            ['file.txt', 0, 1, ['file']],
            ['this.is.a.file.txt', 1, 2, ['is', 'a']],
            ['this.is.a.file.txt', -2, 1, ['file']],
            ['this.is.a.file.txt', -3, -1, ['a', 'file']],
        ];
    }
}