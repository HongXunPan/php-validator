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
        if ($context->current()->exists()) {
            return RuleResult::passPath($context->current());
        }

        return RuleResult::passAndBreak($context->parseRuleArg(), true);
    }
}
