<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

class TimeAfterRule extends AbstractTimeLiteralCompareRule
{
    const KEY = 'timeAfter';
    const MESSAGE = '$paramName must be later than $rule';

    protected static function compare($currentTimestamp, $expectedTimestamp)
    {
        return $currentTimestamp > $expectedTimestamp;
    }
}
