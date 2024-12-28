<?php

namespace Pathogen;


readonly class AbsolutePath extends Path
{
    #[\Override]
    public function format(string $separator): string
    {
        return $separator . parent::format($separator);
    }

    public function toRelative(): RelativePath
    {
        return new RelativePath($this->atoms, $this->hasTrailingSeparator);
    }
}
