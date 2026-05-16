<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;

class EqRule extends AbstractValueRule
{
    const KEY = 'eq';
    const MESSAGE = '$paramName must equal $rule';

    public static function validate(RuleContext $context)
    {
        return $context->value() == $context->parseRuleArg()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
