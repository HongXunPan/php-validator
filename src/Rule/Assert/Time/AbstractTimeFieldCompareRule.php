<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

use HongXunPan\Validator\Rule\Marker\TimeRule;
use HongXunPan\Validator\Rule\AbstractReferencedFieldCompareRule;

abstract class AbstractTimeFieldCompareRule extends AbstractReferencedFieldCompareRule implements TimeRule
{
    protected static function normalizeComparablePair($currentValue, $otherValue)
    {
        $currentTimestamp = strtotime((string)$currentValue);
        $otherTimestamp = strtotime((string)$otherValue);
        if ($currentTimestamp === false || $otherTimestamp === false) {
            return null;
        }

        return array($currentTimestamp, $otherTimestamp);
    }

    protected static function compare($currentValue, $otherValue)
    {
        return false;
    }
}
