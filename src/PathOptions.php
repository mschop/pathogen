<?php

namespace Pathogen;

final readonly class PathOptions
{
    function __construct(
        public bool $isCaseSensitive,
        public string $atomSeparator,
    )
    {
    }
}