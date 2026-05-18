<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

class TimeAfterOrEqualRule extends AbstractTimeLiteralCompareRule
{
    const KEY = 'timeAfterOrEqual';
    const MESSAGE = '$paramName must be later than or equal to $rule';

    protected static function compare($currentTimestamp, $expectedTimestamp)
    {
        return $currentTimestamp >= $expectedTimestamp;
    }
}
