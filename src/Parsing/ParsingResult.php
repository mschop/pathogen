<?php

namespace Mschop\Pathogen\Parsing;

use Mschop\Pathogen\PathType;

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