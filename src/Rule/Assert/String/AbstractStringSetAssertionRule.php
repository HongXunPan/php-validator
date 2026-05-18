<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\StringSetArgument;
use HongXunPan\Validator\Rule\Argument\StringSetArgumentParser;
use HongXunPan\Validator\Rule\Marker\StringRule;

abstract class AbstractStringSetAssertionRule extends AbstractPresentValueAssertionRule implements StringRule
{
    const ARGUMENT_PARSER = StringSetArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        $argument = $context->parsedRuleArg();
        if (!is_string($value) || !$argument instanceof StringSetArgument) {
            return RuleResult::fail($value);
        }

        foreach ($argument->values() as $needle) {
            if (static::matchesNeedle($value, $needle)) {
                return RuleResult::pass($value);
            }
        }

        return RuleResult::fail($value);
    }

    /**
     * @param string $value
     * @param string $needle
     *
     * @return bool
     */
    abstract protected static function matchesNeedle($value, $needle);
}
