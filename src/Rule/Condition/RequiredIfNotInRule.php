<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgumentParser;
use HongXunPan\Validator\Rule\Concern\BuildsFieldExpectedLiteralSetRule;

class RequiredIfNotInRule extends AbstractConditionalFieldPresenceRule
{
    use BuildsFieldExpectedLiteralSetRule;

    const KEY = 'requiredIfNotIn';
    const MESSAGE = '$paramName is required';
    const ARGUMENT_PARSER = FieldExpectedLiteralSetArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedNotIn($context)) {
            return $result;
        }

        if (!$context->current()->exists()) {
            return RuleResult::failPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
