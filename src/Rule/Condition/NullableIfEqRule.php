<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgumentParser;

class NullableIfEqRule extends AbstractConditionalPresentValueGuardRule
{
    const KEY = 'nullableIfEq';
    const MESSAGE = '$paramName nullable';
    const ARGUMENT_PARSER = FieldExpectedLiteralArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedEq($context)) {
            return $result;
        }

        if ($context->value() === null) {
            return RuleResult::passAndBreak(null, $context->fieldExists());
        }

        return RuleResult::pass($context->value(), $context->fieldExists());
    }
}
