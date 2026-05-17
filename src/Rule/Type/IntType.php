<?php

namespace HongXunPan\Validator\Rule\Type;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class IntType extends AbstractPresentValueAssertionRule
{
    const KEY = 'int';
    const MESSAGE = '$paramName must be integer';

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        $passed = $value !== null
            && $value !== ''
            && $value == intval($value);

        return $passed
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }
}
