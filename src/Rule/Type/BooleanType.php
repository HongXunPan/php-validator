<?php

namespace HongXunPan\Validator\Rule\Type;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class BooleanType extends AbstractPresentValueAssertionRule
{
    const KEY = 'boolean';
    const MESSAGE = '$paramName must be boolean';

    public static function validate(RuleContext $context)
    {
        return is_bool($context->value())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
