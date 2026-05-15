<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

class GteFieldRule extends AbstractNumericFieldCompareRule
{
    const KEY = 'gteField';
    const MESSAGE = '$paramName must be greater than or equal to $rule';

    protected static function compare($currentValue, $otherValue)
    {
        return $currentValue >= $otherValue;
    }
}
