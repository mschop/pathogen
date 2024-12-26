<?php

namespace Mschop\Pathogen\Parsing;

readonly class ParseOptions
{
    /**
     * @param string[] $separators
     */
    public function __construct(
        public array $separators = ['/', '\\'],
        public bool $parseWindowsDrive = false,
    )
    {
    }

    public function getPrimarySeparator(): string
    {
        return $this->separators[array_key_first($this->separators)];
    }

    /**
     * @return string[]
     */
    public function getAlternativeSelectors(): array
    {
        return array_slice($this->separators, 1);
    }
}