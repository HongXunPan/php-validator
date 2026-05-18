<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgumentParser;

class NullableIfNotInRule extends AbstractConditionalPresentValueGuardRule
{
    const KEY = 'nullableIfNotIn';
    const MESSAGE = '$paramName nullable';
    const ARGUMENT_PARSER = FieldExpectedLiteralSetArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedNotIn($context)) {
            return $result;
        }

        if ($context->value() === null) {
            return RuleResult::passAndBreakPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
