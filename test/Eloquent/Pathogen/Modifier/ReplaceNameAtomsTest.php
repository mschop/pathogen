<?php

namespace Pathogen\Modifier;

use Pathogen\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ReplaceNameAtomsTest extends TestCase
{
    #[DataProvider('data')]
    public function test(string $path, int $index, iterable $replacement, ?int $length, string $expectedResult)
    {
        $path = Path::fromString($path);
        $actualResult = $path->replaceNameAtoms($index, $replacement, $length)->format('/');
        $this->assertEquals($expectedResult, $actualResult);
    }

    public static function data(): array
    {
        return [
            ['/path/to/file.php', 0, ['it', 'worked', 'php'], null, '/path/to/it.worked.php'],
            ['/path/to/file.php', 1, ['it', 'worked', 'php'], null, '/path/to/file.it.worked.php'],
            ['/path/to/myfile.html.twig', 1, ['it', 'worked'], 1, '/path/to/myfile.it.worked.twig'],
        ];
    }
}