<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class NumericRule extends AbstractPresentValueAssertionRule implements \HongXunPan\Validator\Rule\Marker\NumericRule
{
    const KEY = 'numeric';
    const MESSAGE = '$paramName must be numeric';

    public static function validate(RuleContext $context)
    {
        return self::isNumber($context->value())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected static function isNumber($value)
    {
        return is_int($value) || is_float($value);
    }
}
