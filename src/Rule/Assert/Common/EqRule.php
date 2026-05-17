<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class EqRule extends AbstractPresentValueAssertionRule
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
