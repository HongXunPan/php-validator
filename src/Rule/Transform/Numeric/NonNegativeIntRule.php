<?php

namespace HongXunPan\Validator\Rule\Transform\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\NumericRule;
use HongXunPan\Validator\Rule\ValueMaterializationRuleInterface;

class NonNegativeIntRule extends AbstractValueRule implements NumericRule, ValueMaterializationRuleInterface
{
    const KEY = 'nonNegativeInt';
    const MESSAGE = '$paramName must be non-negative integer';

    public static function validate(RuleContext $context)
    {
        if (!static::isIntegerValue($context->value())) {
            return RuleResult::fail($context->value());
        }

        $value = (int)$context->value();

        return $value >= 0
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
