<?php

namespace Pathogen;


readonly class AbsoluteDriveAnchoredPath extends AbsolutePath implements DriveAnchoredInterface
{
    use DriveAnchoredTrait;

    public function __construct(array $atoms, bool $hasTrailingSeparator, string $drive)
    {
        parent::__construct($atoms, $hasTrailingSeparator);
        $this->drive = $drive;
    }

    public function toRelative(): RelativeDriveAnchoredPath
    {
        return new RelativeDriveAnchoredPath(
            $this->atoms,
            $this->hasTrailingSeparator,
            $this->drive,
        );
    }
}