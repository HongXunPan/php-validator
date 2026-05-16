<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;

class NeqRule extends AbstractValueRule
{
    const KEY = 'neq';
    const MESSAGE = '$paramName must not equal $rule';

    public static function validate(RuleContext $context)
    {
        return $context->value() != $context->parseRuleArg()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
