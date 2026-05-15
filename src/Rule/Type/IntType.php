<?php

namespace HongXunPan\Validator\Rule\Type;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;

class IntType extends AbstractValueRule
{
    const KEY = 'int';
    const MESSAGE = '$paramName must be integer';

    public static function validate($context)
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
