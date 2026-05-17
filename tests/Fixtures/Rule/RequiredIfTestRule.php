<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractFieldPresenceAssertionRule;

class RequiredIfTestRule extends AbstractFieldPresenceAssertionRule
{
    const KEY = 'requiredIfTest';
    const MESSAGE = '$paramName is required';

    public static function validate(RuleContext $context)
    {
        list($fieldPath, $expectedValue) = array_pad(explode(',', (string)$context->ruleArg(), 2), 2, '');
        $otherValue = $context->getMaterializedTargetValue($fieldPath);

        if (!$otherValue->exists() || $otherValue->value() != $expectedValue) {
            return RuleResult::pass($context->value(), $context->fieldExists());
        }

        if (!$context->fieldExists()) {
            return RuleResult::fail($context->value(), false);
        }

        return RuleResult::pass($context->value(), true);
    }
}
