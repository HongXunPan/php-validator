<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgumentParser;

class RequiredIfEqRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'requiredIfEq';
    const MESSAGE = '$paramName is required';
    const ARGUMENT_PARSER = FieldExpectedLiteralArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedEq($context)) {
            return $result;
        }

        if (!$context->fieldExists()) {
            return RuleResult::fail($context->value(), false);
        }

        return RuleResult::pass($context->value(), true);
    }
}
