<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

class TimeBeforeRule extends AbstractTimeLiteralCompareRule
{
    const KEY = 'timeBefore';
    const MESSAGE = '$paramName must be earlier than $rule';

    protected static function compare($currentTimestamp, $expectedTimestamp)
    {
        return $currentTimestamp < $expectedTimestamp;
    }
}
