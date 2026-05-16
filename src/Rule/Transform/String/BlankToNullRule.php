<?php

namespace HongXunPan\Validator\Rule\Transform\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\StringRule;
use HongXunPan\Validator\Rule\ValueMaterializationRuleInterface;

class BlankToNullRule extends AbstractValueRule implements StringRule, ValueMaterializationRuleInterface
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
