<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

class TimeAfterFieldRule extends AbstractTimeFieldCompareRule
{
    const KEY = 'timeAfterField';
    const MESSAGE = '$paramName must be later than $rule';

    protected static function compare($currentValue, $otherValue)
    {
        return $currentValue > $otherValue;
    }
}
