<?php


namespace Eloquent\Pathogen;


/**
 * Class InfectionHelper
 * @package Eloquent\Pathogen
 *
 * This class helps with things, that are reported by infection but are in fact false positives.
 */
class InfectionHelper
{
    public static function array_fill(int $num, $value)
    {
        return array_fill(0, $num, $value);
    }
}