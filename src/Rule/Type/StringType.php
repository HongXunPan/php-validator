<?php

namespace HongXunPan\Validator\Rule\Type;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;

class StringType extends AbstractValueRule
{
    const KEY = 'string';
    const MESSAGE = '$paramName must be string';

    public static function validate($context)
    {
        return is_string($context->value())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
