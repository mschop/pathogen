<?php

/*
 * This file is part of the Pathogen package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pathogen\Exception;

use Exception;

/**
 * An undefined atom was requested.
 */
final class UndefinedAtomException extends Exception
{
    private int $index;

    /**
     * Construct a new undefined atom exception.
     *
     * @param integer        $index    The requested atom index.
     * @param Exception|null $previous The cause, if available.
     */
    public function __construct(int $index, Exception $previous = null)
    {
        $this->index = $index;

        parent::__construct(
            sprintf(
                'No atom defined for index %s.',
                var_export($index, true)
            ),
            0,
            $previous
        );
    }

    /**
     * Get the requested atom index.
     *
     * @return int The requested index.
     */
    public function index(): int
    {
        return $this->index;
    }
}
