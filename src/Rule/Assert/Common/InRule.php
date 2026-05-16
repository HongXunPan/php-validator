<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;

class InRule extends AbstractValueRule
{
    const KEY = 'in';
    const MESSAGE = '$paramName must be in $rule';

    public static function validate(RuleContext $context)
    {
        $expect = $context->parseRuleArg();
        if (!is_array($expect)) {
            return RuleResult::fail($context->value());
        }

        return in_array($context->value(), $expect)
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
