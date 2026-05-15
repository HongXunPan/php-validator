<?php

namespace HongXunPan\Validator\Rule\Transform\Common;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;

class NullableRule extends AbstractValueRule
{
    const KEY = 'nullable';
    const MESSAGE = '$paramName validate failed';

    public static function validate($context)
    {
        if ($context->value() === null) {
            return RuleResult::passAndBreak(null);
        }

        return RuleResult::pass($context->value());
    }
}
