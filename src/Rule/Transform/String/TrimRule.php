<?php

namespace HongXunPan\Validator\Rule\Transform\String;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class TrimRule extends AbstractValueRule implements StringRule
{
    const KEY = 'trim';
    const MESSAGE = '$paramName must be string';

    public static function validate($context)
    {
        if (!is_string($context->value())) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass(trim($context->value()));
    }
}
