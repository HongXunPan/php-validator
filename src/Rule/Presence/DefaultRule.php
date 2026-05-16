<?php

namespace HongXunPan\Validator\Rule\Presence;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresenceRule;
use HongXunPan\Validator\Rule\ValueMaterializationRuleInterface;

class DefaultRule extends AbstractPresenceRule implements ValueMaterializationRuleInterface
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
