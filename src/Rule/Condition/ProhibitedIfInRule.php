<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgumentParser;

class ProhibitedIfInRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'prohibitedIfIn';
    const MESSAGE = '$paramName is prohibited';
    const ARGUMENT_PARSER = FieldExpectedLiteralSetArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedIn($context)) {
            return $result;
        }

        if ($context->current()->exists()) {
            return RuleResult::failPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
