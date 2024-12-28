<?php

namespace Pathogen\Parsing;

use Pathogen\PathType;

readonly class ParsingResult
{
    function __construct(
        public array $atoms,
        public PathType $pathType,
        public ?string $drive,
        public bool $hasTrailingSeparator,
    )
    {
    }
}