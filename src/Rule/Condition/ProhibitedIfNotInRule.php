<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgumentParser;

class ProhibitedIfNotInRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'prohibitedIfNotIn';
    const MESSAGE = '$paramName is prohibited';
    const ARGUMENT_PARSER = FieldExpectedLiteralSetArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedNotIn($context)) {
            return $result;
        }

        if ($context->fieldExists()) {
            return RuleResult::fail($context->value(), true);
        }

        return RuleResult::pass($context->value(), false);
    }
}
