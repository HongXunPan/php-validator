<?php

namespace HongXunPan\Validator\Rule\Presence;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractMissingValueCreationRule;

class DefaultRule extends AbstractMissingValueCreationRule
{
    const KEY = 'default';
    const MESSAGE = '$paramName default failed';

    public static function validate(RuleContext $context)
    {
        if ($context->fieldExists()) {
            return RuleResult::pass($context->value(), true);
        }

        return RuleResult::passAndBreak($context->parseRuleArg(), true);
    }
}
