<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

class LteFieldRule extends AbstractNumericFieldCompareRule
{
    const KEY = 'lteField';
    const MESSAGE = '$paramName must be less than or equal to $rule';

    protected static function compare($currentValue, $otherValue)
    {
        return $currentValue <= $otherValue;
    }
}
