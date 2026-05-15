<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class NonBlankRule extends AbstractValueRule implements StringRule
{
    const KEY = 'nonBlank';
    const MESSAGE = '$paramName can not be blank';

    public static function validate($context)
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
