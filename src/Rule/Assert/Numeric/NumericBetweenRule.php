<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\NumericRangeArgument;
use HongXunPan\Validator\Rule\Argument\NumericRangeArgumentParser;
use HongXunPan\Validator\Rule\Marker\NumericRule;

class NumericBetweenRule extends AbstractPresentValueAssertionRule implements NumericRule
{
    const KEY = 'numericBetween';
    const MESSAGE = '$paramName must be between $rule';
    const ARGUMENT_PARSER = NumericRangeArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        $range = $context->parsedRuleArg();
        if (!self::isNumber($value) || !$range instanceof NumericRangeArgument) {
            return RuleResult::fail($value);
        }

        return $value >= $range->min() && $value <= $range->max()
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private static function isNumber($value)
    {
        return is_int($value) || is_float($value);
    }
}
