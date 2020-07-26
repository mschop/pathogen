<?php

/*
 * This file is part of the Pathogen package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pathogen;

use Traversable;
use Eloquent\Pathogen\Exception\EmptyPathAtomException;
use Eloquent\Pathogen\Exception\InvalidPathAtomExceptionInterface;
use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\Exception\PathAtomContainsSeparatorException;
use Eloquent\Pathogen\Factory\PathFactoryInterface;
use Eloquent\Pathogen\Normalizer\PathNormalizerInterface;
use Eloquent\Pathogen\Resolver\BasePathResolverInterface;

/**
 * Abstract base class for implementing PathInterface.
 */
abstract class AbstractPath implements PathInterface
{
    private array $atoms;
    private bool $hasTrailingSeparator;

    /**
     * The character used to separate path atoms.
     */
    const ATOM_SEPARATOR = '/';

    /**
     * The character used to separate path name atoms.
     */
    const EXTENSION_SEPARATOR = '.';

    /**
     * The atom used to represent 'parent'.
     */
    const PARENT_ATOM = '..';

    /**
     * The atom used to represent 'self'.
     */
    const SELF_ATOM = '.';

    /**
     * Construct a new path instance.
     *
     * @param iterable<string> $atoms                The path atoms.
     * @param boolean|null  $hasTrailingSeparator True if this path has a trailing separator.
     *
     * @throws Exception\InvalidPathAtomExceptionInterface If any of the supplied path atoms are invalid.
     */
    public function __construct(iterable $atoms, bool $hasTrailingSeparator = null)
    {
        if (null === $hasTrailingSeparator) {
            $hasTrailingSeparator = false;
        }

        $this->atoms = $this->normalizeAtoms($atoms);
        $this->hasTrailingSeparator = $hasTrailingSeparator === true;
    }

    // Implementation of PathInterface =========================================

    /**
     * Get the atoms of this path.
     *
     * For example, the path '/foo/bar' has the atoms 'foo' and 'bar'.
     *
     * @return array<integer,string> The atoms of this path as an array of strings.
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
     * @return mixed The path atom, or $default if no atom is defined for the supplied index.
     */
    public function atomAtDefault(int $index, string $default = null): ?string
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
    public function sliceAtoms(int $index, int $length = null): array
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
        return count($this->atoms()) > 0;
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

    /**
     * Generate a string representation of this path.
     *
     * @return string A string representation of this path.
     */
    public function string(): string
    {
        return
            implode(static::ATOM_SEPARATOR, $this->atoms()) .
            ($this->hasTrailingSeparator() ? static::ATOM_SEPARATOR : '');
    }

    /**
     * Generate a string representation of this path.
     *
     * @return string A string representation of this path.
     */
    public function __toString()
    {
        return $this->string();
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
        return explode(static::EXTENSION_SEPARATOR, $this->name());
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
     * @param integer $index   The index to search for.
     * @param string   $default The default value to return if no atom is defined for the supplied index.
     *
     * @return string The path name atom, or $default if no atom is defined for the supplied index.
     */
    public function nameAtomAtDefault(int $index, string $default = null): ?string
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
    public function sliceNameAtoms(int $index, int $length = null): array
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
        $atoms = $this->nameAtoms();
        if (count($atoms) > 1) {
            array_pop($atoms);

            return implode(static::EXTENSION_SEPARATOR, $atoms);
        }

        return $atoms[0];
    }

    /**
     * Get this path's name, excluding all extensions.
     *
     * @return string The last atom of this path, excluding any extensions. If this path has no atoms, an empty string is returned.
     */
    public function namePrefix(): string
    {
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
        return count($this->nameAtoms()) > 1;
    }

    /**
     * Determine if this path contains a substring.
     *
     * @param string       $needle        The substring to search for.
     * @param boolean $caseSensitive True if case sensitive.
     *
     * @return boolean True if this path contains the substring.
     */
    public function contains(string $needle, bool $caseSensitive = false): bool
    {
        if ('' === $needle) {
            return true;
        }

        if ($caseSensitive) {
            return false !== strstr($this->string(), $needle);
        }

        return false !== stristr($this->string(), $needle);
    }

    /**
     * Determine if this path starts with a substring.
     *
     * @param string       $needle        The substring to search for.
     * @param boolean $caseSensitive True if case sensitive.
     *
     * @return boolean True if this path starts with the substring.
     */
    public function startsWith(string $needle, bool $caseSensitive = false): bool
    {
        if ('' === $needle) {
            return true;
        }
        return InfectionHelper::string_starts_with($this, $needle, $caseSensitive);
    }

    /**
     * Determine if this path ends with a substring.
     *
     * @param string       $needle        The substring to search for.
     * @param boolean $caseSensitive True if case sensitive.
     *
     * @return boolean True if this path ends with the substring.
     */
    public function endsWith(string $needle, bool $caseSensitive = false): bool
    {
        return InfectionHelper::string_starts_with(strrev($this), strrev($needle), $caseSensitive);
    }

    /**
     * Determine if this path matches a wildcard pattern.
     *
     * @param string       $pattern       The pattern to check against.
     * @param boolean $caseSensitive True if case sensitive.
     * @param integer|null $flags         Additional flags.
     *
     * @return boolean True if this path matches the pattern.
     */
    public function matches(string $pattern, bool $caseSensitive = false, int $flags = null)
    {
        if (null === $flags) {
            $flags = 0;
        }
        if (!$caseSensitive) {
            $flags = $flags | FNM_CASEFOLD;
        }

        return fnmatch($pattern, $this->string(), $flags);
    }

    /**
     * Determine if this path matches a regular expression.
     *
     * @param string       $pattern  The pattern to check against.
     * @param array|null   &$matches Populated with the pattern matches.
     * @param integer|null $flags    Additional flags.
     * @param integer|null $offset   Start searching from this byte offset.
     *
     * @return boolean True if this path matches the pattern.
     */
    public function matchesRegex(
        string $pattern,
        array &$matches = null,
        int $flags = null,
        int $offset = null
    ): bool {
        if (null === $flags) {
            $flags = 0;
        }
        if (null === $offset) {
            $offset = 0;
        }

        return 1 === preg_match(
            $pattern,
            $this->string(),
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
        if (empty($needle)) {
            return true;
        }

        $name = $this->name();

        if (!$caseSensitive) {
            $name = mb_strtolower($name);
            $needle = mb_strtolower($needle);
        }

        return strstr($name, $needle) !== false;
    }

    /**
     * Determine if this path's name starts with a substring.
     *
     * @param string       $needle        The substring to search for.
     * @param boolean $caseSensitive True if case sensitive.
     *
     * @return boolean True if this path's name starts with the substring.
     */
    public function nameStartsWith(string $needle, bool $caseSensitive = false): bool
    {
        if ('' === $needle) {
            return true;
        }

        if ($caseSensitive) {
            return 0 === mb_strpos($this->name(), $needle);
        }

        return 0 === mb_stripos($this->name(), $needle);
    }

    /**
     * Determine if this path's name matches a wildcard pattern.
     *
     * @param string       $pattern       The pattern to check against.
     * @param boolean $caseSensitive True if case sensitive.
     * @param integer|null $flags         Additional flags.
     *
     * @return boolean True if this path's name matches the pattern.
     */
    public function nameMatches(string $pattern, bool $caseSensitive = false, int $flags = null): bool
    {
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
     * @param string       $pattern  The pattern to check against.
     * @param array|null   &$matches Populated with the pattern matches.
     * @param integer|null $flags    Additional flags.
     * @param integer|null $offset   Start searching from this byte offset.
     *
     * @return boolean True if this path's name matches the pattern.
     */
    public function nameMatchesRegex(
        string $pattern,
        array &$matches = null,
        int $flags = null,
        int $offset = null
    ): bool {
        if (null === $flags) {
            $flags = 0;
        }
        if (null === $offset) {
            $offset = 0;
        }

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
     * @param integer|null $numLevels The number of levels up. Defaults to 1.
     *
     * @return PathInterface The parent of this path $numLevels up.
     * @throws InvalidPathStateException
     */
    public function parent(int $numLevels = null): PathInterface
    {
        if (null === $numLevels) {
            $numLevels = 1;
        }

        return $this->createPath(
            array_merge(
                $this->atoms(),
                array_fill(0, $numLevels, static::PARENT_ATOM)
            ),
            $this instanceof AbsolutePathInterface,
            false
        );
    }

    /**
     * Strips the trailing slash from this path.
     *
     * @return PathInterface A new path instance with the trailing slash removed from this path. If this path has no trailing slash, the path is returned unmodified.
     */
    public function stripTrailingSlash(): PathInterface
    {
        if (!$this->hasTrailingSeparator()) {
            return $this;
        }

        return $this->createPath(
            $this->atoms(),
            $this instanceof AbsolutePathInterface,
            false
        );
    }

    /**
     * Strips the last extension from this path.
     *
     * @return PathInterface A new path instance with the last extension removed from this path. If this path has no extensions, the path is returned unmodified.
     */
    public function stripExtension(): PathInterface
    {
        return $this->replaceExtension(null);
    }

    /**
     * Strips all extensions from this path.
     *
     * @return PathInterface A new path instance with all extensions removed from this path. If this path has no extensions, the path is returned unmodified.
     */
    public function stripNameSuffix(): PathInterface
    {
        return $this->replaceNameSuffix(null);
    }

    /**
     * Joins one or more atoms to this path.
     *
     * @param string     ...$atom            A path atom to append.
     *
     * @return PathInterface                               A new path with the supplied atom(s) suffixed to this path.
     * @throws Exception\InvalidPathAtomExceptionInterface If any joined atoms are invalid.
     */
    public function joinAtoms(string ...$atom): PathInterface
    {
        return $this->joinAtomSequence($atom);
    }

    /**
     * Joins a sequence of atoms to this path.
     *
     * @param mixed<string> $atoms The path atoms to append.
     *
     * @return PathInterface                               A new path with the supplied sequence of atoms suffixed to this path.
     * @throws Exception\InvalidPathAtomExceptionInterface If any joined atoms are invalid.
     */
    public function joinAtomSequence(iterable $atoms): PathInterface
    {
        if (!is_array($atoms)) {
            $atoms = iterator_to_array($atoms);
        }

        return $this->createPath(
            array_merge($this->atoms(), $atoms),
            $this instanceof AbsolutePathInterface,
            false
        );
    }

    /**
     * Joins the supplied path to this path.
     *
     * @param RelativePathInterface $path The path whose atoms should be joined to this path.
     *
     * @return PathInterface A new path with the supplied path suffixed to this path.
     */
    public function join(RelativePathInterface $path): PathInterface
    {
        return $this->joinAtomSequence($path->atoms());
    }

    /**
     * Adds a trailing slash to this path.
     *
     * @return PathInterface A new path instance with a trailing slash suffixed to this path.
     */
    public function joinTrailingSlash(): PathInterface
    {
        if ($this->hasTrailingSeparator()) {
            return $this;
        }

        return $this->createPath(
            $this->atoms(),
            $this instanceof AbsolutePathInterface,
            true
        );
    }

    /**
     * Joins one or more extensions to this path.
     *
     * @param string     ...$extension            An extension to append.
     *
     * @return PathInterface                               A new path instance with the supplied extensions suffixed to this path.
     * @throws Exception\InvalidPathAtomExceptionInterface If the suffixed extensions cause the atom to be invalid.
     */
    public function joinExtensions(string ...$extension): PathInterface
    {
        return $this->joinExtensionSequence(func_get_args());
    }

    /**
     * Joins a sequence of extensions to this path.
     *
     * @param mixed<string> $extensions The extensions to append.
     *
     * @return PathInterface                               A new path instance with the supplied extensions suffixed to this path.
     * @throws Exception\InvalidPathAtomExceptionInterface If the suffixed extensions cause the atom to be invalid.
     */
    public function joinExtensionSequence(iterable $extensions): PathInterface
    {
        if (!is_array($extensions)) {
            $extensions = iterator_to_array($extensions);
        }

        $atoms = $this->nameAtoms();
        if (array('', '') === $atoms) {
            array_pop($atoms);
        }

        return $this->replaceName(
            implode(
                static::EXTENSION_SEPARATOR,
                array_merge($atoms, $extensions)
            )
        );
    }

    /**
     * Suffixes this path's name with a supplied string.
     *
     * @param string $suffix The string to suffix to the path name.
     *
     * @return PathInterface                               A new path instance with the supplied string suffixed to the last path atom.
     * @throws Exception\InvalidPathAtomExceptionInterface If the suffix causes the atom to be invalid.
     */
    public function suffixName(string $suffix): PathInterface
    {
        $name = $this->name();
        if (static::SELF_ATOM === $name) {
            return $this->replaceName($suffix);
        }

        return $this->replaceName($name . $suffix);
    }

    /**
     * Prefixes this path's name with a supplied string.
     *
     * @param string $prefix The string to prefix to the path name.
     *
     * @return PathInterface                               A new path instance with the supplied string prefixed to the last path atom.
     * @throws Exception\InvalidPathAtomExceptionInterface If the prefix causes the atom to be invalid.
     */
    public function prefixName(string $prefix): PathInterface
    {
        $name = $this->name();
        if (static::SELF_ATOM === $name) {
            return $this->replaceName($prefix);
        }

        return $this->replaceName($prefix . $name);
    }

    /**
     * Replace a section of this path with the supplied atom sequence.
     *
     * @param integer       $index       The start index of the replacement.
     * @param iterable<string> $replacement The replacement atom sequence.
     * @param integer|null  $length      The number of atoms to replace. If $length is null, the entire remainder of the path will be replaced.
     *
     * @return PathInterface A new path instance that has a portion of this path's atoms replaced with a different sequence of atoms.
     */
    public function replace(int $index, iterable $replacement, int $length = null): PathInterface
    {
        $atoms = $this->atoms();

        if (!is_array($replacement)) {
            $replacement = iterator_to_array($replacement);
        }
        if (null === $length) {
            $length = count($atoms);
        }

        array_splice($atoms, $index, $length, $replacement);

        return $this->createPath(
            $atoms,
            $this instanceof AbsolutePathInterface,
            false
        );
    }

    /**
     * Replace this path's name.
     *
     * @param string $name The new path name.
     *
     * @return PathInterface A new path instance with the supplied name replacing the existing one.
     */
    public function replaceName(string $name): PathInterface
    {
        $atoms = $this->atoms();
        $numAtoms = count($atoms);

        if ($numAtoms > 0) {
            if ('' === $name) {
                array_pop($atoms);
            } else {
                $atoms[$numAtoms - 1] = $name;
            }
        } elseif ('' !== $name) {
            $atoms[] = $name;
        }

        return $this->createPath(
            $atoms,
            $this instanceof AbsolutePathInterface,
            false
        );
    }

    /**
     * Replace this path's name, but keep the last extension.
     *
     * @param string $nameWithoutExtension The replacement string.
     *
     * @return PathInterface A new path instance with the supplied name replacing the portion of the existing name preceding the last extension.
     */
    public function replaceNameWithoutExtension(string $nameWithoutExtension): PathInterface
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
     * @return PathInterface A new path instance with the supplied name prefix replacing the existing one.
     */
    public function replaceNamePrefix(string $namePrefix): PathInterface
    {
        return $this->replaceNameAtoms(0, array($namePrefix), 1);
    }

    /**
     * Replace all of this path's extensions.
     *
     * @param string|null $nameSuffix The replacement string, or null to remove all extensions.
     *
     * @return PathInterface A new path instance with the supplied name suffix replacing the existing one.
     */
    public function replaceNameSuffix(?string $nameSuffix): PathInterface
    {
        $atoms = $this->nameAtoms();
        if (array('', '') === $atoms) {
            if (null === $nameSuffix) {
                return $this;
            }

            return $this->replaceName(
                static::EXTENSION_SEPARATOR . $nameSuffix
            );
        }

        $numAtoms = count($atoms);

        if (null === $nameSuffix) {
            $replacement = array();
        } else {
            $replacement = array($nameSuffix);
        }
        array_splice($atoms, 1, count($atoms), $replacement);

        return $this->replaceName(implode(self::EXTENSION_SEPARATOR, $atoms));
    }

    /**
     * Replace this path's last extension.
     *
     * @param string|null $extension The replacement string, or null to remove the last extension.
     *
     * @return PathInterface A new path instance with the supplied extension replacing the existing one.
     */
    public function replaceExtension(?string $extension): PathInterface
    {
        $atoms = $this->nameAtoms();
        if (array('', '') === $atoms) {
            if (null === $extension) {
                return $this;
            }

            return $this->replaceName(
                static::EXTENSION_SEPARATOR . $extension
            );
        }

        $numAtoms = count($atoms);

        if ($numAtoms > 1) {
            if (null === $extension) {
                $replacement = array();
            } else {
                $replacement = array($extension);
            }

            array_splice($atoms, -1, $numAtoms, $replacement);
        } elseif (null !== $extension) {
            $atoms[] = $extension;
        }

        return $this->replaceName(implode(self::EXTENSION_SEPARATOR, $atoms));
    }

    /**
     * Replace a section of this path's name with the supplied name atom
     * sequence.
     *
     * @param integer       $index       The start index of the replacement.
     * @param iterable <string> $replacement The replacement name atom sequence.
     * @param integer|null  $length      The number of atoms to replace. If $length is null, the entire remainder of the path name will be replaced.
     *
     * @return PathInterface A new path instance that has a portion of this name's atoms replaced with a different sequence of atoms.
     */
    public function replaceNameAtoms(int $index, iterable $replacement, int $length = null): PathInterface
    {
        $atoms = $this->nameAtoms();

        if ($replacement instanceof Traversable) {
            $replacement = iterator_to_array($replacement);
        }
        if (null === $length) {
            $length = count($atoms);
        }

        array_splice($atoms, $index, $length, $replacement);

        return $this->replaceName(implode(self::EXTENSION_SEPARATOR, $atoms));
    }

    /**
     * Normalize this path to its most canonical form.
     *
     * @return PathInterface The normalized path.
     */
    public function normalize(): PathInterface
    {
        return static::normalizer()->normalize($this);
    }

    // Implementation details ==================================================

    /**
     * Normalizes and validates a sequence of path atoms.
     *
     * This method is called internally by the constructor upon instantiation.
     * It can be overridden in child classes to change how path atoms are
     * normalized and/or validated.
     *
     * @param mixed<string> $atoms The path atoms to normalize.
     *
     * @return iterable<string>                                The normalized path atoms.
     * @throws Exception\EmptyPathAtomException             If any path atom is empty.
     * @throws Exception\PathAtomContainsSeparatorException If any path atom contains a separator.
     */
    protected function normalizeAtoms(iterable $atoms): iterable
    {
        $normalizedAtoms = array();
        foreach ($atoms as $atom) {
            $this->validateAtom($atom);
            $normalizedAtoms[] = $atom;
        }

        return $normalizedAtoms;
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
    protected function validateAtom(string $atom)
    {
        if ('' === $atom) {
            throw new EmptyPathAtomException;
        } elseif (false !== strpos($atom, static::ATOM_SEPARATOR)) {
            throw new PathAtomContainsSeparatorException($atom);
        }
    }

    /**
     * Creates a new path instance of the most appropriate type.
     *
     * This method is called internally every time a new path instance is
     * created as part of another method call. It can be overridden in child
     * classes to change which classes are used when creating new path
     * instances.
     *
     * @param mixed<string> $atoms                The path atoms.
     * @param boolean       $isAbsolute           True if the new path should be absolute.
     * @param boolean  $hasTrailingSeparator True if the new path should have a trailing separator.
     *
     * @return PathInterface The newly created path instance.
     *
     * @throws InvalidPathAtomExceptionInterface If any of the supplied atoms are invalid.
     * @throws InvalidPathStateException         If the supplied arguments would produce an invalid path.
     */
    protected function createPath(
        iterable $atoms,
        bool $isAbsolute,
        bool $hasTrailingSeparator = false
    ): PathInterface {
        return static::factory()->createFromAtoms(
            $atoms,
            $isAbsolute,
            $hasTrailingSeparator
        );
    }

    /**
     * Get the most appropriate path factory for this type of path.
     *
     * @return Factory\PathFactoryInterface The path factory.
     */
    protected static function factory(): PathFactoryInterface
    {
        return Factory\PathFactory::instance();
    }

    /**
     * Get the most appropriate path normalizer for this type of path.
     *
     * @return Normalizer\PathNormalizerInterface The path normalizer.
     */
    protected static function normalizer(): PathNormalizerInterface
    {
        return Normalizer\PathNormalizer::instance();
    }

    /**
     * Get the most appropriate base path resolver for this type of path.
     *
     * @return Resolver\BasePathResolverInterface The base path resolver.
     */
    protected static function resolver(): BasePathResolverInterface
    {
        return Resolver\BasePathResolver::instance();
    }
}
