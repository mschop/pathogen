<?php

namespace Eloquent\Pathogen;

use SimplifiedIsolator\Isolator as BaseIsolator;

class Isolator extends BaseIsolator
{
    static ?Isolator $instance = null;

    static function get(Isolator $isolator = null): Isolator
    {
        if ($isolator) {
            return $isolator;
        }

        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}