<?php

namespace Pathogen;


use Pathogen\Exception\InvalidPathStateException;

readonly class AbsolutePath extends Path
{
    public function __construct(array $atoms, bool $hasTrailingSeparator)
    {
        parent::__construct($atoms, $hasTrailingSeparator);
        $this->validateIsInbound();
    }

    #[\Override]
    public function format(?string $separator = null): string
    {
        if ($separator === null) {
            $separator = static::DEFAULT_SEPARATOR;
        }
        return $separator . parent::format($separator);
    }

    public function toRelative(): RelativePath
    {
        return new RelativePath($this->atoms, $this->hasTrailingSeparator);
    }

    protected function validateIsInbound(): void
    {
        $normalized = $this->getNormalizedAtoms();

        if (empty($normalized)) {
            return;
        }

        if ($normalized[array_key_first($normalized)] === static::PARENT_ATOM) {
            throw new InvalidPathStateException("The provided atoms would go outbound of an absolute path, which would be fine for relative paths, but not for absolute paths.");
        }
    }
}
