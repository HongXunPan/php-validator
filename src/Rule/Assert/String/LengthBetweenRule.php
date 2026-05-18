<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\IntRangeArgument;
use HongXunPan\Validator\Rule\Argument\IntRangeArgumentParser;
use HongXunPan\Validator\Rule\Marker\StringRule;

class LengthBetweenRule extends AbstractPresentValueAssertionRule implements StringRule
{
    const KEY = 'lengthBetween';
    const MESSAGE = '$paramName length must be between $rule';
    const ARGUMENT_PARSER = IntRangeArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        $range = $context->parsedRuleArg();
        if (!is_string($value) || !$range instanceof IntRangeArgument) {
            return RuleResult::fail($value);
        }

        $length = self::length($value);

        return $length >= $range->min() && $length <= $range->max()
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private static function length($value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return iconv_strlen($value);
    }
}
