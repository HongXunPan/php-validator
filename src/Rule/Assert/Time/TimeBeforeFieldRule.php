<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

class TimeBeforeFieldRule extends AbstractTimeFieldCompareRule
{
    const KEY = 'timeBeforeField';
    const MESSAGE = '$paramName must be earlier than $rule';

    protected static function compare($currentValue, $otherValue)
    {
        return $currentValue < $otherValue;
    }
}
