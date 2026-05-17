<?php

namespace HongXunPan\Validator\Rule\Type;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class ArrayType extends AbstractPresentValueAssertionRule
{
    const KEY = 'array';
    const MESSAGE = '$paramName must be array';

    public static function validate(RuleContext $context)
    {
        return is_array($context->value())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
