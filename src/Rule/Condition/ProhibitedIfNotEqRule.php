<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgumentParser;

class ProhibitedIfNotEqRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'prohibitedIfNotEq';
    const MESSAGE = '$paramName is prohibited';
    const ARGUMENT_PARSER = FieldExpectedLiteralArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedNotEq($context)) {
            return $result;
        }

        if ($context->fieldExists()) {
            return RuleResult::fail($context->value(), true);
        }

        return RuleResult::pass($context->value(), false);
    }
}
