<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

class GtFieldRule extends AbstractNumericFieldCompareRule
{
    const KEY = 'gtField';
    const MESSAGE = '$paramName must be greater than $rule';

    protected static function compare($currentValue, $otherValue)
    {
        return $currentValue > $otherValue;
    }
}
