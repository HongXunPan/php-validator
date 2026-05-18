<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class StartsWithRule extends AbstractStringSetAssertionRule
{
    const KEY = 'startsWith';
    const MESSAGE = '$paramName must start with $rule';

    protected static function matchesNeedle($value, $needle)
    {
        return strncmp($value, $needle, strlen($needle)) === 0;
    }
}
