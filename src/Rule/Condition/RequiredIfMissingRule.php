<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;

class RequiredIfMissingRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'requiredIfMissing';
    const MESSAGE = '$paramName is required';
    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedMissing($context)) {
            return $result;
        }

        if (!$context->current()->exists()) {
            return RuleResult::failPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
