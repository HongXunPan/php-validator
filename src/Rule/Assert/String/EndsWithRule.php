<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class EndsWithRule extends AbstractStringSetAssertionRule
{
    const KEY = 'endsWith';
    const MESSAGE = '$paramName must end with $rule';

    protected static function matchesNeedle($value, $needle)
    {
        return substr($value, -strlen($needle)) === $needle;
    }
}
