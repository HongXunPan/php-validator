<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

class TimeAfterOrEqualFieldRule extends AbstractTimeFieldCompareRule
{
    const KEY = 'timeAfterOrEqualField';
    const MESSAGE = '$paramName must be later than or equal to $rule';

    protected static function compare($currentValue, $otherValue)
    {
        return $currentValue >= $otherValue;
    }
}
