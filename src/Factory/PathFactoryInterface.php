<?php

namespace Pathogen\Factory;

use Pathogen\AbsolutePath;
use Pathogen\Exception\MissingDriveException;
use Pathogen\Exception\PathTypeMismatchException;
use Pathogen\Path;
use Pathogen\PathType;
use Pathogen\RelativePath;

interface PathFactoryInterface
{
    /**
     * @template T of Path
     * @param string $path
     * @param class-string<T> $type
     * @return T
     * @throws MissingDriveException
     * @throws PathTypeMismatchException
     */
    public function fromString(string $path, string $type): Path;

    /**
     * @template T of Path
     * @param array $atoms
     * @param class-string<T> $type
     * @params ?string $drive
     * @return T
     * @throws MissingDriveException
     */
    public function fromAtoms(array $atoms, string $type, bool $hasTrailingSeparator, ?string $drive = null): Path;
}