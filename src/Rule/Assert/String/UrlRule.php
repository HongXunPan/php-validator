<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class UrlRule extends AbstractPresentValueAssertionRule implements StringRule
{
    const KEY = 'url';
    const MESSAGE = '$paramName must be valid URL';

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }
}
