<?php

namespace HongXunPan\Validator\Rule\Presence;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresenceRule;

class RequiredRule extends AbstractPresenceRule
{
    const KEY = 'required';
    const MESSAGE = '$paramName is required';

    public static function validate($context)
    {
        if (!$context->fieldExists()) {
            return RuleResult::fail($context->value(), false);
        }

        return RuleResult::pass($context->value(), true);
    }
}
