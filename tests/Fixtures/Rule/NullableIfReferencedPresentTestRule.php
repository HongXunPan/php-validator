<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;
use HongXunPan\Validator\Rule\Condition\AbstractConditionalPresentValueGuardRule;

class NullableIfReferencedPresentTestRule extends AbstractConditionalPresentValueGuardRule
{
    const KEY = 'nullableIfReferencedPresentTest';
    const MESSAGE = '$paramName nullable';
    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedPresent($context)) {
            return $result;
        }

        if ($context->value() === null) {
            return RuleResult::passAndBreakPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
