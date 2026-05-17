<?php

namespace HongXunPan\Validator\Rule\Transform\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueGuardRule;

class NullableRule extends AbstractPresentValueGuardRule
{
    const KEY = 'nullable';
    const MESSAGE = '$paramName validate failed';

    public static function validate(RuleContext $context)
    {
        if ($context->value() === null) {
            return RuleResult::passAndBreak(null);
        }

        return RuleResult::pass($context->value());
    }
}
