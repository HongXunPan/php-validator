<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueGuardRule;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgument;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgumentParser;

class NullableIfInRule extends AbstractPresentValueGuardRule
{
    const KEY = 'nullableIfIn';
    const MESSAGE = '$paramName nullable';
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

        if ($context->value() === null) {
            return RuleResult::passAndBreak(null, $context->fieldExists());
        }

        return RuleResult::pass($context->value(), $context->fieldExists());
    }
}
