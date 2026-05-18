<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

class TimeBeforeOrEqualRule extends AbstractTimeLiteralCompareRule
{
    const KEY = 'timeBeforeOrEqual';
    const MESSAGE = '$paramName must be earlier than or equal to $rule';

    protected static function compare($currentTimestamp, $expectedTimestamp)
    {
        return $currentTimestamp <= $expectedTimestamp;
    }
}
