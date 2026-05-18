<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\LiteralSetArgument;
use HongXunPan\Validator\Rule\Argument\LiteralSetArgumentParser;

class NotInRule extends AbstractPresentValueAssertionRule
{
    const KEY = 'notIn';
    const MESSAGE = '$paramName must not be in $rule';
    const ARGUMENT_PARSER = LiteralSetArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $argument = $context->parsedRuleArg();
        if (!$argument instanceof LiteralSetArgument) {
            return RuleResult::fail($context->value());
        }

        return !in_array($context->value(), $argument->values(), true)
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
