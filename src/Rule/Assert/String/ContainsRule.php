<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class ContainsRule extends AbstractStringSetAssertionRule
{
    const KEY = 'contains';
    const MESSAGE = '$paramName must contain $rule';

    protected static function matchesNeedle($value, $needle)
    {
        return strpos($value, $needle) !== false;
    }
}
