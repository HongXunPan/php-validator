<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

class LtFieldRule extends AbstractNumericFieldCompareRule
{
    const KEY = 'ltField';
    const MESSAGE = '$paramName must be less than $rule';

    protected static function compare($currentValue, $otherValue)
    {
        return $currentValue < $otherValue;
    }
}
