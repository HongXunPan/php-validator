<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\NumericRule;

class LtRule extends AbstractValueRule implements NumericRule
{
    const KEY = 'lt';
    const MESSAGE = '$paramName must be less than $rule';

    public static function validate(RuleContext $context)
    {
        return $context->value() < $context->parseRuleArg()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
