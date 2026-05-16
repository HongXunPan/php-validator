<?php

namespace HongXunPan\Validator\Rule\Type;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;

class TimeType extends AbstractValueRule
{
    const KEY = 'time';
    const MESSAGE = '$paramName must be time';

    public static function validate(RuleContext $context)
    {
        return strtotime((string)$context->value()) !== false
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
