<?php

namespace Mschop\Pathogen;

readonly class RelativeDriveAnchoredPath extends RelativePath implements DriveAnchoredInterface
{
    use DriveAnchoredTrait;

    public function __construct(array $atoms, bool $hasTrailingSeparator, string $drive)
    {
        parent::__construct($atoms, $hasTrailingSeparator);
        $this->drive = $drive;
    }

    public function toAbsolute(): AbsoluteDriveAnchoredPath
    {
        return new AbsoluteDriveAnchoredPath(
            $this->atoms,
            $this->hasTrailingSeparator,
            $this->drive,
        );
    }
}