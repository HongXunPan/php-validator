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
        if (!$context->fieldExists()) {
            return RuleResult::fail($context->value(), false);
        }

        return RuleResult::pass($context->value(), true);
    }
}
