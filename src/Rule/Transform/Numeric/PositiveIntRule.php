<?php

namespace HongXunPan\Validator\Rule\Transform\Numeric;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\NumericRule;

class PositiveIntRule extends AbstractValueRule implements NumericRule
{
    const KEY = 'positiveInt';
    const MESSAGE = '$paramName must be positive integer';

    public static function validate($context)
    {
        if (!static::isIntegerValue($context->value())) {
            return RuleResult::fail($context->value());
        }

        $value = (int)$context->value();

        return $value > 0
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    protected static function isIntegerValue($value)
    {
        return $value !== null
            && $value !== ''
            && $value == intval($value);
    }
}
