<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class JsonRule extends AbstractPresentValueAssertionRule implements StringRule
{
    const KEY = 'json';
    const MESSAGE = '$paramName must be valid JSON';

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }
}
