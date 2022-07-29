<?php

namespace Mschop\Pathogen\Factory;

use Mschop\Pathogen\AbsolutePath;
use Mschop\Pathogen\Exception\MissingDriveException;
use Mschop\Pathogen\Exception\PathTypeMismatch;
use Mschop\Pathogen\Path;
use Mschop\Pathogen\PathType;
use Mschop\Pathogen\RelativePath;

interface PathFactoryInterface
{
    /**
     * @template T of Path
     * @param string $path
     * @param class-string<T> $type
     * @return T
     * @throws MissingDriveException
     * @throws PathTypeMismatch
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