<?php

namespace HongXunPan\Validator\Rule\Presence;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresenceRule;

class DefaultRule extends AbstractPresenceRule
{
    const KEY = 'default';
    const MESSAGE = '$paramName default failed';

    public static function validate($context)
    {
        if ($context->fieldExists()) {
            return RuleResult::pass($context->value(), true);
        }

        return RuleResult::passAndBreak($context->parseRuleArg(), true);
    }
}
