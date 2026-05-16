<?php

namespace HongXunPan\Validator\Rule\Transform\Time;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\TimeRule;
use HongXunPan\Validator\Rule\ValueMaterializationRuleInterface;

class FormatTimeRule extends AbstractValueRule implements TimeRule, ValueMaterializationRuleInterface
{
    const KEY = 'formatTime';
    const MESSAGE = '$paramName must be time';

    public static function validate(RuleContext $context)
    {
        $timestamp = strtotime((string)$context->value());
        if ($timestamp === false) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass(date((string)$context->ruleArg(), $timestamp));
    }
}
