<?php

namespace HongXunPan\Validator\Rule\Presence;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractFieldPresenceAssertionRule;

class RequiredRule extends AbstractFieldPresenceAssertionRule
{
    const KEY = 'required';
    const MESSAGE = '$paramName is required';

    public static function validate(RuleContext $context)
    {
        if (!$context->current()->exists()) {
            return RuleResult::failPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
