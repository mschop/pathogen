<?php

namespace Pathogen;

trait DriveAnchoredTrait
{
    protected readonly string $drive;

    public function getDrive(): string
    {
        return $this->drive;
    }

    #[\Override]
    public function format(string $separator): string
    {
        return strtoupper($this->drive) . ':' . parent::format($separator);
    }
}