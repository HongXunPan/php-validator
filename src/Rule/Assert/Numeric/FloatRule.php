<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Marker\NumericRule as NumericRuleMarker;

class FloatRule extends AbstractPresentValueAssertionRule implements NumericRuleMarker
{
    const KEY = 'float';
    const MESSAGE = '$paramName must be float';

    public static function validate(RuleContext $context)
    {
        return is_float($context->value())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
