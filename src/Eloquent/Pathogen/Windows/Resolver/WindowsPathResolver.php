<?php

/*
 * This file is part of the Pathogen package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pathogen\Windows\Resolver;

use Eloquent\Pathogen\AbsolutePathInterface;
use Eloquent\Pathogen\PathInterface;
use Eloquent\Pathogen\Resolver\PathResolverInterface;
use Eloquent\Pathogen\Windows\RelativeWindowsPathInterface;

/**
 * A path resolver for Windows paths.
 */
class WindowsPathResolver implements PathResolverInterface
{
    /**
     * Get a static instance of this path resolver.
     *
     * @return PathResolverInterface The static path resolver.
     */
    public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Resolve a path against a given base path.
     *
     * @param AbsolutePathInterface $basePath The base path.
     * @param PathInterface         $path     The path to resolve.
     *
     * @return AbsolutePathInterface The resolved path.
     */
    public function resolve(
        AbsolutePathInterface $basePath,
        PathInterface $path
    ) {
        if ($path instanceof AbsolutePathInterface) {
            return $path;
        }
        if ($path instanceof RelativeWindowsPathInterface) {
            if ($path->isAnchored()) {
                return $path->joinDrive($basePath->drive());
            }
            if ($path->hasDrive() && !$path->matchesDrive($basePath->drive())) {
                return $path->toAbsolute();
            }
        }

        return $basePath->join($path);
    }

    private static $instance;
}