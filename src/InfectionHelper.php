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

    /**
     * Infection changes mb_strpos to strpos and vice versa. When testing, whether a string start with another string,
     * this distinction does not make any difference and is therefore not testable.
     *
     * @param string $s
     * @param string $needle
     * @param bool $caseSensitive
     * @return bool
     */
    public static function string_starts_with(string $s, string $needle, bool $caseSensitive = true): bool
    {
        if (empty($needle)) {
            return true;
        }
        return 0 === ($caseSensitive ? mb_strpos($s, $needle) : mb_stripos($s, $needle));
    }
}