<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;

class ProhibitedIfPresentRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'prohibitedIfPresent';
    const MESSAGE = '$paramName is prohibited';
    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedPresent($context)) {
            return $result;
        }

        if ($context->fieldExists()) {
            return RuleResult::fail($context->value(), true);
        }

        return RuleResult::pass($context->value(), false);
    }
}
