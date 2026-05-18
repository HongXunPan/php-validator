<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\NonNegativeIntArgument;
use HongXunPan\Validator\Rule\Argument\NonNegativeIntArgumentParser;
use HongXunPan\Validator\Rule\Marker\NumericRule;

class DecimalPlacesRule extends AbstractPresentValueAssertionRule implements NumericRule
{
    const KEY = 'decimalPlaces';
    const MESSAGE = '$paramName must have at most $rule decimal places';
    const ARGUMENT_PARSER = NonNegativeIntArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        $argument = $context->parsedRuleArg();
        if (!self::isNumber($value) || !$argument instanceof NonNegativeIntArgument) {
            return RuleResult::fail($value);
        }

        return self::hasAtMostDecimalPlaces($value, $argument->value())
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

    /**
     * @param int|float $value
     * @param int $places
     *
     * @return bool
     */
    private static function hasAtMostDecimalPlaces($value, $places)
    {
        if (is_int($value)) {
            return true;
        }

        $rounded = round($value, $places);

        return abs($rounded - $value) <= 0.000000001;
    }
}
