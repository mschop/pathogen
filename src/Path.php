<?php

namespace Pathogen;

use Pathogen\Exception\EmptyPathAtomException;
use Pathogen\Exception\InvalidArgumentException;
use Pathogen\Exception\InvalidPathStateException;
use Pathogen\Exception\MissingDriveException;
use Pathogen\Exception\PathAtomContainsSeparatorException;
use Pathogen\Exception\PathTypeMismatchException;
use Pathogen\Factory\PathFactory;

readonly class Path
{
    public const string EXTENSION_SEPARATOR = '.';
    public const string DEFAULT_SEPARATOR = '/';
    public const string PARENT_ATOM = '..';
    public const string SELF_ATOM = '.';

    function __construct(
        protected array $atoms,
        protected bool $hasTrailingSeparator,
    )
    {
        foreach($atoms as $atom) {
            $this->validateAtom($atom);
        }
    }

    /**
     * @throws MissingDriveException
     * @throws PathTypeMismatchException
     */
    public static function fromString(string $path): static
    {
        return PathFactory::getDefaultInstance()->fromString($path, static::class);
    }

    /**
     * Get the atoms of this path.
     *
     * For example, the path '/foo/bar' has the atoms 'foo' and 'bar'.
     *
     * @return array<integer, string> The atoms of this path as an array of strings.
     */
    public function atoms(): array
    {
        return $this->atoms;
    }

    /**
     * Get a single path atom by index.
     *
     * @param integer $index The index to search for.
     *
     * @return string                           The path atom.
     * @throws Exception\UndefinedAtomException If the index does not exist in this path's atoms.
     */
    public function atomAt(int $index): string
    {
        $atom = $this->atomAtDefault($index);
        if (null === $atom) {
            throw new Exception\UndefinedAtomException($index);
        }

        return $atom;
    }

    /**
     * Get a single path atom by index, falling back to a default if the index
     * is undefined.
     *
     * @param integer $index   The index to search for.
     * @param mixed   $default The default value to return if no atom is defined for the supplied index.
     *
     * @return ?string The path atom, or $default if no atom is defined for the supplied index.
     */
    public function atomAtDefault(int $index, ?string $default = null): ?string
    {
        $atoms = $this->atoms();
        if ($index < 0) {
            $index = count($atoms) + $index;
        }

        if (array_key_exists($index, $atoms)) {
            return $atoms[$index];
        }

        return $default;
    }

    /**
     * Get a subset of the atoms of this path.
     *
     * @param integer      $index  The index of the first atom.
     * @param integer|null $length The maximum number of atoms.
     *
     * @return array<integer,string> An array of strings representing the subset of path atoms.
     */
    public function sliceAtoms(int $index, ?int $length = null): array
    {
        $atoms = $this->atoms();
        if (null === $length) {
            $length = count($atoms);
        }

        return array_slice($atoms, $index, $length);
    }

    /**
     * Determine if this path has any atoms.
     *
     * @return boolean True if this path has at least one atom.
     */
    public function hasAtoms(): bool
    {
        return !empty($this->atoms());
    }

    /**
     * Determine if this path has a trailing separator.
     *
     * @return boolean True if this path has a trailing separator.
     */
    public function hasTrailingSeparator(): bool
    {
        return $this->hasTrailingSeparator;
    }

    public function format(string $separator): string
    {
        return implode($separator, $this->atoms()) . ($this->hasTrailingSeparator() ? $separator : '');
    }

    /**
     * Generate a string representation of this path.
     *
     * @return string A string representation of this path.
     *
     * @deprecated Please use method `format` instead.
     */
    public function string(): string
    {
        trigger_error('The method `->string()` is deprecated. Use `->format()` instead.', E_USER_DEPRECATED);
        return $this->format(static::DEFAULT_SEPARATOR);
    }

    /**
     * Generate a string representation of this path.
     *
     * @return string A string representation of this path.
     */
    public function __toString()
    {
        return $this->format(static::DEFAULT_SEPARATOR);
    }

    /**
     * Get this path's name.
     *
     * @return string The last path atom if one exists, otherwise an empty string.
     */
    public function name(): string
    {
        $atoms = $this->atoms();
        $numAtoms = count($atoms);

        if ($numAtoms > 0) {
            return $atoms[$numAtoms - 1];
        }

        return '';
    }

    /**
     * Get this path's name atoms.
     *
     * For example, the path name 'foo.bar' has the atoms 'foo' and 'bar'.
     *
     * @return array<integer,string> The atoms of this path's name as an array of strings.
     */
    public function nameAtoms(): array
    {
        return array_values(array_filter(explode(static::EXTENSION_SEPARATOR, $this->name())));
    }

    /**
     * Get a single path name atom by index.
     *
     * @param integer $index The index to search for.
     *
     * @return string                           The path name atom.
     * @throws Exception\UndefinedAtomException If the index does not exist in this path's name atoms.
     */
    public function nameAtomAt(int $index): string
    {
        $atom = $this->nameAtomAtDefault($index);
        if (null === $atom) {
            throw new Exception\UndefinedAtomException($index);
        }

        return $atom;
    }

    /**
     * Get a single path name atom by index, falling back to a default if the
     * index is undefined.
     *
     * @param integer $index The index to search for.
     * @param ?string $default The default value to return if no atom is defined for the supplied index.
     * @return ?string The path name atom, or $default if no atom is defined for the supplied index.
     */
    public function nameAtomAtDefault(int $index, ?string $default = null): ?string
    {
        $atoms = $this->nameAtoms();
        if ($index < 0) {
            $index = count($atoms) + $index;
        }

        if (array_key_exists($index, $atoms)) {
            return $atoms[$index];
        }

        return $default;
    }

    /**
     * Get a subset of this path's name atoms.
     *
     * @param integer      $index  The index of the first atom.
     * @param integer|null $length The maximum number of atoms.
     *
     * @return array<integer,string> An array of strings representing the subset of path name atoms.
     */
    public function sliceNameAtoms(int $index, ?int $length = null): array
    {
        $atoms = $this->nameAtoms();
        if (null === $length) {
            $length = count($atoms);
        }

        return array_slice($atoms, $index, $length);
    }

    /**
     * Get this path's name, excluding the last extension.
     *
     * @return string The last atom of this path, excluding the last extension. If this path has no atoms, an empty string is returned.
     */
    public function nameWithoutExtension(): string
    {
        if (!str_contains($this->name(), '.')) {
            return $this->name();
        }

        return implode(
            static::EXTENSION_SEPARATOR,
            $this->sliceNameAtoms(0, -1),
        );
    }

    /**
     * Get this path's name, excluding all extensions.
     *
     * @return string The last atom of this path, excluding any extensions. If this path has no atoms, an empty string is returned.
     */
    public function namePrefix(): string
    {
        if (str_starts_with($this->name(), self::EXTENSION_SEPARATOR)) {
            return '';
        }

        $atoms = $this->nameAtoms();

        return $atoms[0];
    }

    /**
     * Get all of this path's extensions.
     *
     * @return string|null The extensions of this path's last atom. If the last atom has no extensions, or this path has no atoms, this method will return null.
     */
    public function nameSuffix(): ?string
    {
        $atoms = $this->nameAtoms();
        if (count($atoms) > 1) {
            array_shift($atoms);

            return implode(static::EXTENSION_SEPARATOR, $atoms);
        }

        return null;
    }

    /**
     * Get this path's last extension.
     *
     * @return string|null The last extension of this path's last atom. If the last atom has no extensions, or this path has no atoms, this method will return null.
     */
    public function extension(): ?string
    {
        $atoms = $this->nameAtoms();
        $numParts = count($atoms);

        if ($numParts > 1) {
            return $atoms[$numParts - 1];
        }

        return null;
    }

    /**
     * Determine if this path has any extensions.
     *
     * @return boolean True if this path's last atom has any extensions.
     */
    public function hasExtension(): bool
    {
        return str_contains($this->name(), static::EXTENSION_SEPARATOR);
    }

    /**
     * Determine if this path contains a substring.
     *
     * @param string $needle The substring to search for. The needle will be normalized according to static::DEFAULT_SEPARATOR
     * @param boolean $caseSensitive True if case sensitive.
     * @return boolean True if this path contains the substring.
     */
    public function contains(string $needle, bool $caseSensitive = false): bool
    {
        $needle = $this->normalizePath($needle);

        $haystack = $this->format(static::DEFAULT_SEPARATOR);

        if (!$caseSensitive) {
            $needle = mb_strtolower($needle);
            $haystack = mb_strtolower($haystack);
        }

        return str_contains($haystack, $needle);
    }

    /**
     * Determine if this path starts with a substring.
     *
     * @param string $needle The substring to search for.
     * @param boolean $caseSensitive True if case-sensitive.
     *
     * @return boolean True if this path starts with the substring.
     */
    public function startsWith(string $needle, bool $caseSensitive = false): bool
    {
        if ('' === $needle) {
            return true;
        }

        $needle = $this->normalizePath($needle);
        $haystack = $this->format(static::DEFAULT_SEPARATOR);

        if (!$caseSensitive) {
            $needle = mb_strtolower($needle);
            $haystack = mb_strtolower($haystack);
        }

        return str_starts_with($haystack, $needle);
    }

    /**
     * Determine if this path ends with a substring.
     *
     * @param string $needle The substring to search for.
     * @param boolean $caseSensitive True if case sensitive.
     *
     * @return boolean True if this path ends with the substring.
     */
    public function endsWith(string $needle, bool $caseSensitive = false): bool
    {
        $needle = $this->normalizePath($needle);
        $haystack = $this->format(static::DEFAULT_SEPARATOR);

        if (!$caseSensitive) {
            $needle = mb_strtolower($needle);
            $haystack = mb_strtolower($haystack);
        }
        return str_ends_with($haystack, $needle);
    }

    /**
     * Determine if this path matches a wildcard pattern.
     *
     * @param string $pattern The pattern to check against.
     * @param boolean $caseSensitive True if case-sensitive.
     * @param integer $flags Additional flags.
     * @return boolean True if this path matches the pattern.
     */
    public function matches(string $pattern, bool $caseSensitive = false, int $flags = 0): bool
    {
        if ($flags & FNM_CASEFOLD) {
            throw new InvalidArgumentException('Setting the flag FNM_CASEFOLD for `fnmatch` is not supported, because this could cause unexpected behavior regarding $caseSensitive flag');
        }

        $pattern = $this->normalizePath($pattern);

        if (!$caseSensitive) {
            $flags = $flags | FNM_CASEFOLD;
        }

        return fnmatch($pattern, $this->format(static::DEFAULT_SEPARATOR), $flags);
    }

    /**
     * Determine if this path matches a regular expression.
     *
     * @param string $pattern  The pattern to check against.
     * @param array|null &$matches Populated with the pattern matches.
     * @param integer $flags    Additional flags.
     * @param integer $offset   Start searching from this byte offset.
     *
     * @return boolean True if this path matches the pattern.
     */
    public function matchesRegex(
        string $pattern,
        ?array &$matches = null,
        int $flags = 0,
        int $offset = 0
    ): bool {
        return 1 === preg_match(
            $pattern,
            $this->format(static::DEFAULT_SEPARATOR),
            $matches,
            $flags,
            $offset
        );
    }

    /**
     * Determine if this path's name contains a substring.
     *
     * @param string       $needle        The substring to search for.
     * @param boolean $caseSensitive True if case sensitive.
     *
     * @return boolean True if this path's name contains the substring.
     */
    public function nameContains(string $needle, bool $caseSensitive = false): bool
    {
        if ($needle === '') {
            return true;
        }

        $name = $this->name();

        if (!$caseSensitive) {
            $name = mb_strtolower($name);
            $needle = mb_strtolower($needle);
        }

        return str_contains($name, $needle);
    }

    /**
     * Determine if this path's name starts with a substring.
     *
     * @param string $needle        The substring to search for.
     * @param boolean $caseSensitive True if case-sensitive.
     * @return boolean True if this path's name starts with the substring.
     */
    public function nameStartsWith(string $needle, bool $caseSensitive = false): bool
    {
        if ('' === $needle) {
            return true;
        }

        $haystack = $this->name();

        if (!$caseSensitive) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }

        return str_contains($haystack, $needle);
    }

    /**
     * Determine if this path's name matches a wildcard pattern.
     *
     * @param string $pattern The pattern to check against.
     * @param boolean $caseSensitive True if case-sensitive.
     * @param integer|null $flags Additional flags.
     * @return boolean True if this path's name matches the pattern.
     */
    public function nameMatches(string $pattern, bool $caseSensitive = false, ?int $flags = null): bool
    {
        if ($flags & FNM_CASEFOLD) {
            throw new InvalidArgumentException('Setting the flag FNM_CASEFOLD for `fnmatch` is not supported, because this could cause unexpected behavior regarding $caseSensitive flag');
        }

        if (null === $flags) {
            $flags = 0;
        }
        if (!$caseSensitive) {
            $flags = $flags | FNM_CASEFOLD;
        }

        return fnmatch($pattern, $this->name(), $flags);
    }

    /**
     * Determine if this path's name matches a regular expression.
     *
     * @param string $pattern  The pattern to check against.
     * @param array|null &$matches Populated with the pattern matches.
     * @param integer $flags Additional flags.
     * @param integer $offset Start searching from this byte offset.
     *
     * @return boolean True if this path's name matches the pattern.
     */
    public function nameMatchesRegex(
        string $pattern,
        ?array &$matches = null,
        int $flags = 0,
        int $offset = 0
    ): bool {
        return 1 === preg_match(
                $pattern,
                $this->name(),
                $matches,
                $flags,
                $offset
            );
    }

    /**
     * Get the parent of this path a specified number of levels up.
     *
     * @param integer $numLevels The number of levels up. Defaults to 1.
     *
     * @return static The parent of this path $numLevels up.
     * @throws InvalidPathStateException
     */
    public function parent(int $numLevels = 1): static
    {
        $atoms = $this->atoms();
        while(count($atoms) > 0 && $numLevels > 0) {
            array_pop($atoms);
            $numLevels--;
        }

        if ($numLevels > 0) {
            if ($this instanceof AbsolutePath) {
                throw new InvalidPathStateException('Absolute paths to not have a parent for the root path');
            }
            $atoms = array_merge(
                $atoms,
                array_fill(0, $numLevels, static::PARENT_ATOM),
            );
        }

        return $this instanceof DriveAnchoredInterface
            ? new static($atoms, false, $this->getDrive())
            : new static($atoms, false);
    }

    /**
     * Strips the trailing slash from this path.
     *
     * @return static A new path instance with the trailing slash removed from this path. If this path has no trailing slash, the path is returned unmodified.
     */
    public function stripTrailingSlash(): static
    {
        if (!$this->hasTrailingSeparator()) {
            return $this;
        }

        return $this instanceof DriveAnchoredInterface
            ? new static($this->atoms(), false, $this->getDrive())
            : new static($this->atoms(), false);
    }

    /**
     * Strips the last extension from this path.
     *
     * @return static A new path instance with the last extension removed from this path. If this path has no extensions, the path is returned unmodified.
     */
    public function stripExtension(): static
    {
        $atoms = $this->atoms();

        if (empty($atoms)) {
            return $this;
        }

        $name = $atoms[array_key_last($atoms)];

        if (!str_contains($name, static::EXTENSION_SEPARATOR)) {
            return $this;
        }

        $nameAtoms = explode(static::EXTENSION_SEPARATOR, $name);
        array_pop($nameAtoms);
        $newName = implode(
            static::EXTENSION_SEPARATOR,
            $nameAtoms,
        );

        return $this->replaceName($newName);
    }

    /**
     * Strips all extensions from this path.
     *
     * @return static A new path instance with all extensions removed from this path. If this path has no extensions, the path is returned unmodified.
     */
    public function stripNameSuffix(): static
    {
        $atoms = $this->atoms;

        if (empty($atoms)) {
            return $this;
        }

        $name = $atoms[array_key_last($atoms)];

        if (!str_contains($name, static::EXTENSION_SEPARATOR)) {
            return $this;
        }

        $nameAtoms = explode(static::EXTENSION_SEPARATOR, $name);

        $newName = $nameAtoms[array_key_first($nameAtoms)];

        return $this->replaceName($newName);
    }

    /**
     * Joins one or more atoms to this path.
     *
     * @param string ...$atom A path atom to append.
     * @return static A new path with the supplied atom(s) suffixed to this path.
     */
    public function joinAtoms(string ...$atom): static
    {
        return $this->joinAtomSequence($atom);
    }

    /**
     * Joins a sequence of atoms to this path.
     *
     * @param mixed<string> $atoms The path atoms to append.
     *
     * @return static A new path with the supplied sequence of atoms suffixed to this path.
     */
    public function joinAtomSequence(iterable $atoms): static
    {
        $combinedAtoms = $this->atoms();
        foreach($atoms as $atom) {
            $combinedAtoms[] = $atom;
        }

        return $this->reCreate($combinedAtoms);
    }

    /**
     * Joins the supplied path to this path.
     *
     * @param RelativePath $path The path whose atoms should be joined to this path.
     * @return static A new path with the supplied path suffixed to this path.
     */
    public function join(RelativePath $path): static
    {
        return $this->joinAtomSequence($path->atoms());
    }

    /**
     * Adds a trailing slash to this path.
     *
     * @return static A new path instance with a trailing slash suffixed to this path.
     */
    public function joinTrailingSlash(): static
    {
        if ($this->hasTrailingSeparator()) {
            return $this;
        }

        return $this->reCreate(hasTrailingSeparator: true);
    }

    /**
     * Joins one or more extensions to this path.
     *
     * @param string ...$extension An extension to append.
     * @return static A new path instance with the supplied extensions suffixed to this path.
     */
    public function joinExtensions(string ...$extension): static
    {
        return $this->joinExtensionSequence($extension);
    }

    /**
     * Joins a sequence of extensions to this path.
     *
     * @param iterable<string> $extensions The extensions to append.
     * @return static A new path instance with the supplied extensions suffixed to this path.
     */
    public function joinExtensionSequence(iterable $extensions): static
    {
        $atoms = $this->nameAtoms();

        foreach($extensions as $extension) {
            $atoms[] = $extension;
        }

        return $this->replaceName(
            implode(
                static::EXTENSION_SEPARATOR,
                $atoms,
            )
        );
    }

    /**
     * Suffixes this path's name with a supplied string.
     *
     * @param string $suffix The string to suffix to the path name.
     * @return static A new path instance with the supplied string suffixed to the last path atom.
     */
    public function suffixName(string $suffix): static
    {
        return $this->replaceName($this->name() . $suffix);
    }

    /**
     * Prefixes this path's name with a supplied string.
     *
     * @param string $prefix The string to prefix to the path name.
     * @return static A new path instance with the supplied string prefixed to the last path atom.
     */
    public function prefixName(string $prefix): static
    {
        return $this->replaceName($prefix . $this->name());
    }

    /**
     * Replace a section of this path with the supplied atom sequence.
     *
     * @param integer $index The start index of the replacement.
     * @param iterable<string> $replacement The replacement atom sequence.
     * @param integer|null $length The number of atoms to replace. If $length is null, the entire remainder of the path will be replaced.
     * @return static A new path instance that has a portion of this path's atoms replaced with a different sequence of atoms.
     */
    public function replace(int $index, iterable $replacement, ?int $length = null): static
    {
        $atoms = array_values($this->atoms());

        foreach($replacement as $replacementAtom) {
            if (!isset($atoms[$index])) {
                break;
            }
            $atoms[$index++] = $replacementAtom;
        }

        return $this->reCreate($atoms);
    }

    /**
     * Replace this path's name.
     *
     * @param string $name The new path name.
     *
     * @return static A new path instance with the supplied name replacing the existing one.
     */
    public function replaceName(string $name): static
    {
        $atoms = $this->atoms;
        array_pop($atoms);
        $atoms[] = $name;
        return $this->reCreate($atoms);
    }

    /**
     * Replace this path's name, but keep the last extension.
     *
     * @param string $nameWithoutExtension The replacement string.
     *
     * @return static A new path instance with the supplied name replacing the portion of the existing name preceding the last extension.
     */
    public function replaceNameWithoutExtension(string $nameWithoutExtension): static
    {
        $atoms = $this->nameAtoms();
        if (count($atoms) < 2) {
            return $this->replaceName($nameWithoutExtension);
        }

        array_splice($atoms, 0, -1, array($nameWithoutExtension));

        return $this->replaceName(implode(self::EXTENSION_SEPARATOR, $atoms));
    }

    /**
     * Replace this path's name, but keep any extensions.
     *
     * @param string $namePrefix The replacement string.
     *
     * @return static A new path instance with the supplied name prefix replacing the existing one.
     */
    public function replaceNamePrefix(string $namePrefix): static
    {
        $name = $this->name();

        $keep = str_starts_with($name, static::EXTENSION_SEPARATOR)
            ? $name
            : mb_substr($name, mb_strpos($name, static::EXTENSION_SEPARATOR));

        $newName = $namePrefix . $keep;

        return $this->replaceName($newName);
    }

    /**
     * Replace all of this path's extensions.
     *
     * @param string $nameSuffix The replacement string, or null to remove all extensions.
     *
     * @return static A new path instance with the supplied name suffix replacing the existing one.
     */
    public function replaceNameSuffix(string $nameSuffix): static
    {
        if (str_starts_with($nameSuffix, static::EXTENSION_SEPARATOR)) {
            $nameSuffix = mb_substr($nameSuffix, 1);
        }
        $name = $this->name();
        $keep = str_contains($name, static::EXTENSION_SEPARATOR)
            ? mb_substr($name, 0, mb_strpos($name, static::EXTENSION_SEPARATOR))
            : $name;
        return $this->replaceName($keep . static::EXTENSION_SEPARATOR . $nameSuffix);
    }

    /**
     * Replace this path's last extension.
     *
     * @param string $extension The replacement string, or null to remove the last extension.
     *
     * @return static A new path instance with the supplied extension replacing the existing one.
     */
    public function replaceExtension(string $extension): static
    {
        $name = $this->name();
        $keep = str_contains($name, static::EXTENSION_SEPARATOR)
            ? mb_substr($name, 0, mb_strrpos($name, static::EXTENSION_SEPARATOR, -1))
            : $name;
        if (str_starts_with($extension, static::EXTENSION_SEPARATOR)) {
            $extension = mb_substr($extension, 1);
        }
        return $this->replaceName($keep . static::EXTENSION_SEPARATOR . $extension);
    }

    /**
     * Replace a section of this path's name with the supplied name atom
     * sequence.
     *
     * @param integer       $index       The start index of the replacement.
     * @param iterable<string> $replacement The replacement name atom sequence.
     * @param integer|null  $length      The number of atoms to replace. If $length is null, the entire remainder of the path name will be replaced.
     *
     * @return static A new path instance that has a portion of this name's atoms replaced with a different sequence of atoms.
     */
    public function replaceNameAtoms(int $index, iterable $replacement, ?int $length = null): static
    {
        $atoms = $this->nameAtoms();

        if ($replacement instanceof \Traversable) {
            $replacement = iterator_to_array($replacement);
        }
        if (null === $length) {
            $length = count($atoms);
        }

        array_splice($atoms, $index, $length, $replacement);

        return $this->replaceName(implode(self::EXTENSION_SEPARATOR, $atoms));
    }

    /**
     * Removes / Resolves superfluous atoms '.' and '..'.
     * Please note, that a normalized path can still contain '..' atoms at the start because those might not be resolvable.
     *
     * @return static
     */
    public function normalize(): static
    {
        $previousAtoms = array_values($this->atoms());
        $newAtoms = [];
        foreach($previousAtoms as $key => $atom) {
            if ($atom === static::SELF_ATOM) {
                continue;
            } elseif ($atom === static::PARENT_ATOM) {
                $previousIndex = $key - 1;
                if (isset($newAtoms[$previousIndex]) && $newAtoms[$previousIndex] !== static::PARENT_ATOM) {
                    array_pop($newAtoms);
                } else {
                    $newAtoms[$key] = $atom;
                }
            } else {
                $newAtoms[$key] = $atom;
            }
        }
        return $this->reCreate(atoms: array_values($newAtoms), hasTrailingSeparator: $this->hasTrailingSeparator);
    }

    /**
     * Validates a single path atom.
     *
     * This method is called internally by the constructor upon instantiation.
     * It can be overridden in child classes to change how path atoms are
     * validated.
     *
     * @param string $atom The atom to validate.
     *
     * @throws EmptyPathAtomException
     * @throws PathAtomContainsSeparatorException
     */
    protected function validateAtom(string $atom): void
    {
        if ('' === $atom) {
            throw new EmptyPathAtomException;
        } elseif (str_contains($atom, static::DEFAULT_SEPARATOR)) {
            throw new PathAtomContainsSeparatorException($atom);
        }
    }

    /**
     * Creates a path with newly defined atoms but with the same type.
     *
     * @param array|null $atoms
     * @param bool|null $hasTrailingSeparator
     * @return $this
     */
    protected function reCreate(?array $atoms = null, ?bool $hasTrailingSeparator = null): static
    {
        if ($atoms === null) {
            $atoms = $this->atoms();
        }

        if ($hasTrailingSeparator === null) {
            $hasTrailingSeparator = $this->hasTrailingSeparator();
        }

        return $this instanceof DriveAnchoredInterface
            ? new static($atoms, $hasTrailingSeparator, $this->getDrive())
            : new static($atoms, $hasTrailingSeparator);
    }

    protected function normalizePath(string $path): string
    {
        return str_replace(['/', '\\'], static::DEFAULT_SEPARATOR, $path);
    }
}