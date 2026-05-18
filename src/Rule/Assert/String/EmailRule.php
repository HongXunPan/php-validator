<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class EmailRule extends AbstractPresentValueAssertionRule implements StringRule
{
    const KEY = 'email';
    const MESSAGE = '$paramName must be valid email';

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }
}
