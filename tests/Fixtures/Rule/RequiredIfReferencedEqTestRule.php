<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgumentParser;
use HongXunPan\Validator\Rule\Condition\AbstractConditionalFieldPresenceRule;

class RequiredIfReferencedEqTestRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'requiredIfReferencedEqTest';
    const MESSAGE = '$paramName is required';
    const ARGUMENT_PARSER = FieldExpectedLiteralArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedEq($context)) {
            return $result;
        }

        if (!$context->current()->exists()) {
            return RuleResult::failPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
