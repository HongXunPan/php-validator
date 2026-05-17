<?php

namespace HongXunPan\Validator\Rule\Type;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class ListOfType extends AbstractPresentValueAssertionRule
{
    const KEY = 'listOf';
    const MESSAGE = '$paramName must be list';

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_array($value)) {
            return RuleResult::fail($value);
        }

        $expectedIndex = 0;
        foreach ($value as $key => $itemValue) {
            if ((string)$key !== (string)$expectedIndex) {
                return RuleResult::fail($value);
            }

            $expectedIndex++;
        }

        return RuleResult::pass($value);
    }
}
