<?php

namespace HongXunPan\Validator\Rule\Transform\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueNormalizationRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class BlankToNullRule extends AbstractPresentValueNormalizationRule implements StringRule
{
    const KEY = 'blankToNull';
    const MESSAGE = '$paramName must be string';

    public static function validate(RuleContext $context)
    {
        if ($context->value() === null) {
            return RuleResult::pass(null);
        }

        if (!is_string($context->value())) {
            return RuleResult::fail($context->value());
        }

        $value = trim($context->value());

        return RuleResult::pass($value === '' ? null : $value);
    }
}
