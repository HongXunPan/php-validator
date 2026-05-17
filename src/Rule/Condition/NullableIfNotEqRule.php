<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueGuardRule;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgument;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgumentParser;

class NullableIfNotEqRule extends AbstractPresentValueGuardRule
{
    const KEY = 'nullableIfNotEq';
    const MESSAGE = '$paramName nullable';
    const ARGUMENT_PARSER = FieldExpectedLiteralArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $argument = $context->parsedRuleArg();
        if (!$argument instanceof FieldExpectedLiteralArgument) {
            return RuleResult::pass($context->value(), $context->fieldExists());
        }

        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());
        if (!$otherValue->exists() || !ConditionValueMatcher::notEq($otherValue->value(), $argument->expectedValue())) {
            return RuleResult::pass($context->value(), $context->fieldExists());
        }

        if ($context->value() === null) {
            return RuleResult::passAndBreak(null, $context->fieldExists());
        }

        return RuleResult::pass($context->value(), $context->fieldExists());
    }
}
