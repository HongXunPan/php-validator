<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\PositiveNumericArgument;
use HongXunPan\Validator\Rule\Argument\PositiveNumericArgumentParser;
use HongXunPan\Validator\Rule\Marker\NumericRule as NumericRuleMarker;

class MultipleOfRule extends AbstractPresentValueAssertionRule implements NumericRuleMarker
{
    const KEY = 'multipleOf';
    const MESSAGE = '$paramName must be a multiple of $rule';
    const ARGUMENT_PARSER = PositiveNumericArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        $argument = $context->parsedRuleArg();
        if (!self::isNumber($value) || !$argument instanceof PositiveNumericArgument) {
            return RuleResult::fail($value);
        }

        return self::isMultipleOf($value, $argument->value())
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
     * @param int|float $step
     *
     * @return bool
     */
    private static function isMultipleOf($value, $step)
    {
        if (is_int($value) && is_int($step)) {
            return $value % $step === 0;
        }

        $ratio = $value / $step;

        return abs($ratio - round($ratio)) <= 0.000000001;
    }
}
