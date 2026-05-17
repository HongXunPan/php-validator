<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\IntArgument;
use HongXunPan\Validator\Rule\Argument\IntArgumentParser;
use HongXunPan\Validator\Rule\Marker\StringRule;

class MinLengthRule extends AbstractPresentValueAssertionRule implements StringRule
{
    const KEY = 'minLength';
    const MESSAGE = '$paramName length must be at least $rule';
    const ARGUMENT_PARSER = IntArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        return self::length($value) >= self::intValue($context->parsedRuleArg())
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    /**
     * @param mixed $ruleArg
     *
     * @return int
     */
    private static function intValue($ruleArg)
    {
        return $ruleArg instanceof IntArgument
            ? $ruleArg->value()
            : (int)$ruleArg;
    }

    private static function length($value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return iconv_strlen($value);
    }
}
