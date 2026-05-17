<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class NonBlankRule extends AbstractPresentValueAssertionRule implements StringRule
{
    const KEY = 'nonBlank';
    const MESSAGE = '$paramName can not be blank';

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        return trim($value) !== ''
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }
}
