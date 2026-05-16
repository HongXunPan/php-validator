<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresenceRule;
use HongXunPan\Validator\Rule\ConditionalPresenceRuleInterface;

class NullableIfTestRule extends AbstractPresenceRule implements ConditionalPresenceRuleInterface
{
    const KEY = 'nullableIfTest';
    const MESSAGE = '$paramName nullable';

    public static function validate(RuleContext $context)
    {
        list($fieldPath, $expectedValue) = array_pad(explode(',', (string)$context->ruleArg(), 2), 2, '');
        $otherValue = $context->getMaterializedTargetValue($fieldPath);

        if (!$otherValue->exists() || $otherValue->value() != $expectedValue) {
            return RuleResult::pass($context->value(), $context->fieldExists());
        }

        if ($context->value() === null) {
            return RuleResult::passAndBreak(null, $context->fieldExists());
        }

        return RuleResult::pass($context->value(), $context->fieldExists());
    }
}
