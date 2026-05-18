<?php

namespace HongXunPan\Validator\Rule\Collection;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\KeySetArgument;
use HongXunPan\Validator\Rule\Argument\KeySetArgumentParser;

abstract class AbstractArrayKeyRule extends AbstractPresentValueAssertionRule
{
    const ARGUMENT_PARSER = KeySetArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        $argument = $context->parsedRuleArg();
        if (!is_array($value) || !$argument instanceof KeySetArgument) {
            return RuleResult::fail($value);
        }

        return static::matchesKeys($value, $argument->keys())
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    /**
     * @param array<mixed> $value
     * @param array<int, string> $keys
     *
     * @return bool
     */
    abstract protected static function matchesKeys(array $value, array $keys);
}
