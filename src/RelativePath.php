<?php

namespace Mschop\Pathogen;

readonly class RelativePath extends Path
{
    public function toAbsolute(): AbsolutePath
    {
        return new AbsolutePath($this->atoms, $this->hasTrailingSeparator());
    }
}