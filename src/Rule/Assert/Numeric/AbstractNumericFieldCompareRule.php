<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Rule\Marker\NumericRule;
use HongXunPan\Validator\Rule\AbstractReferencedFieldCompareRule;

abstract class AbstractNumericFieldCompareRule extends AbstractReferencedFieldCompareRule implements NumericRule
{
    protected static function normalizeComparablePair($currentValue, $otherValue)
    {
        if (!is_numeric($currentValue) || !is_numeric($otherValue)) {
            return null;
        }

        return array((float)$currentValue, (float)$otherValue);
    }

    protected static function compare($currentValue, $otherValue)
    {
        return false;
    }
}
