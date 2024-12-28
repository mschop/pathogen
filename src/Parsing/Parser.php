<?php

namespace Pathogen\Parsing;

use Pathogen\PathType;

class Parser implements ParserInterface
{
    function parse(string $path, ParseOptions $options): ParsingResult
    {
        $path = trim($path);

        $drive = null;
        if ($options->parseWindowsDrive) {
            $path = explode(':', $path);
            if (preg_match('/[a-z]/i', $path[0])) {
                $drive = strtoupper($path[0]);
                array_shift($path);
            }
            $path = implode($path);
        }

        $pathType = $this->getPathType($path, $options);
        $path = $this->replaceAlternativeSeparators($path, $options);
        $hasTrailingSeparator = str_ends_with($path, $options->getPrimarySeparator());
        $atoms = $this->toAtoms($path, $options);
        $atoms = $this->trimAtoms($atoms);
        $atoms = $this->filterEmptyAtoms($atoms);
        $atoms = $this->filterNoopAtoms($atoms);
        $atoms = $this->reindex($atoms);

        return new ParsingResult($atoms, $pathType, $drive, $hasTrailingSeparator);
    }

    protected function getPathType(string $path, ParseOptions $options): PathType
    {
        $startWithSeparator = false;

        foreach($options->separators as $separator) {
            if (str_starts_with($path, $separator)) {
                $startWithSeparator = true;
                break;
            }
        }

        return match($startWithSeparator) {
            true => PathType::ABSOLUTE,
            false => PathType::RELATIVE,
        };
    }

    protected function replaceAlternativeSeparators(string $path, ParseOptions $options): string
    {
        return str_replace($options->getAlternativeSelectors(), $options->getPrimarySeparator(), $path);
    }

    protected function toAtoms(string $path, ParseOptions $options): array
    {
        return explode($options->getPrimarySeparator(), $path);
    }

    protected function trimAtoms(array $atoms): array
    {
        return array_map('trim', $atoms);
    }

    protected function filterEmptyAtoms(array $atoms): array
    {
        return array_filter($atoms);
    }

    public function reindex(array $atoms): array
    {
        return array_values($atoms);
    }

    public function filterNoopAtoms(array $atoms): array
    {
        return array_filter($atoms, fn(string $x) => $x !== '.');
    }
}