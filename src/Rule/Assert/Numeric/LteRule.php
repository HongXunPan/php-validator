<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\NumericRule;

class LteRule extends AbstractValueRule implements NumericRule
{
    const KEY = 'lte';
    const MESSAGE = '$paramName must be less than or equal to $rule';

    public static function validate($context)
    {
        return $context->value() <= $context->parseRuleArg()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
