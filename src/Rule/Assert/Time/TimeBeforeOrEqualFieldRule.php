<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

class TimeBeforeOrEqualFieldRule extends AbstractTimeFieldCompareRule
{
    const KEY = 'timeBeforeOrEqualField';
    const MESSAGE = '$paramName must be earlier than or equal to $rule';

    protected static function compare($currentValue, $otherValue)
    {
        return $currentValue <= $otherValue;
    }
}
