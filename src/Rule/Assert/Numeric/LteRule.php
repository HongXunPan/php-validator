<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Marker\NumericRule as NumericRuleMarker;

class LteRule extends AbstractPresentValueAssertionRule implements NumericRuleMarker
{
    const KEY = 'lte';
    const MESSAGE = '$paramName must be less than or equal to $rule';

    public static function validate(RuleContext $context)
    {
        return $context->value() <= $context->parseRuleArg()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
