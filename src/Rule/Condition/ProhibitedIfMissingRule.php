<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;

class ProhibitedIfMissingRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'prohibitedIfMissing';
    const MESSAGE = '$paramName is prohibited';
    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedMissing($context)) {
            return $result;
        }

        if ($context->current()->exists()) {
            return RuleResult::failPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
