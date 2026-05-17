<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractFieldPresenceAssertionRule;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgument;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgumentParser;

class ProhibitedIfInRule extends AbstractFieldPresenceAssertionRule
{
    const KEY = 'prohibitedIfIn';
    const MESSAGE = '$paramName is prohibited';
    const ARGUMENT_PARSER = FieldExpectedLiteralSetArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $argument = $context->parsedRuleArg();
        if (!$argument instanceof FieldExpectedLiteralSetArgument) {
            return RuleResult::pass($context->value(), $context->fieldExists());
        }

        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());
        if (!$otherValue->exists() || !ConditionValueMatcher::in($otherValue->value(), $argument->expectedValues())) {
            return RuleResult::pass($context->value(), $context->fieldExists());
        }

        if ($context->fieldExists()) {
            return RuleResult::fail($context->value(), true);
        }

        return RuleResult::pass($context->value(), false);
    }
}
