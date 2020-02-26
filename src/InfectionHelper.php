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
    /**
     * If the first indexes in the array are irrelevant, the mutation testing tool creates false positives when mutating
     * the parameter `start_index` of the `array_fill` method.
     *
     * @param int $num
     * @param $value
     * @return array
     */
    public static function array_fill(int $num, $value)
    {
        return array_fill(0, $num, $value);
    }
}