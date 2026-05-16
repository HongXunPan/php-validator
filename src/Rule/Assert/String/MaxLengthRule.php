<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class MaxLengthRule extends AbstractValueRule implements StringRule
{
    const KEY = 'maxLength';
    const MESSAGE = '$paramName length must be at most $rule';

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        return self::length($value) <= (int)$context->ruleArg()
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    private static function length($value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return iconv_strlen($value);
    }
}
