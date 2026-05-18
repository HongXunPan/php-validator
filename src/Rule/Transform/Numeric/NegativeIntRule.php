<?php

namespace HongXunPan\Validator\Rule\Transform\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;

class NegativeIntRule extends AbstractIntegerTransformRule
{
    const KEY = 'negativeInt';
    const MESSAGE = '$paramName must be negative integer';

    public static function validate(RuleContext $context)
    {
        if (!static::isIntegerValue($context->value())) {
            return RuleResult::fail($context->value());
        }

        $value = static::toInteger($context->value());

        return $value < 0
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }
}
