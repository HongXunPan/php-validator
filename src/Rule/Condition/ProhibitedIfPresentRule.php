<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractFieldPresenceAssertionRule;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgument;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;

class ProhibitedIfPresentRule extends AbstractFieldPresenceAssertionRule
{
    const KEY = 'prohibitedIfPresent';
    const MESSAGE = '$paramName is prohibited';
    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $argument = $context->parsedRuleArg();
        if (!$argument instanceof FieldReferenceArgument) {
            return RuleResult::pass($context->value(), $context->fieldExists());
        }

        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());
        if (!$otherValue->exists()) {
            return RuleResult::pass($context->value(), $context->fieldExists());
        }

        if ($context->fieldExists()) {
            return RuleResult::fail($context->value(), true);
        }

        return RuleResult::pass($context->value(), false);
    }
}
