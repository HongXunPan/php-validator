<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\TimeLiteralArgument;
use HongXunPan\Validator\Rule\Argument\TimeLiteralArgumentParser;
use HongXunPan\Validator\Rule\Marker\TimeRule;

abstract class AbstractTimeLiteralCompareRule extends AbstractPresentValueAssertionRule implements TimeRule
{
    const ARGUMENT_PARSER = TimeLiteralArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $currentTimestamp = strtotime((string)$context->value());
        $argument = $context->parsedRuleArg();
        if ($currentTimestamp === false || !$argument instanceof TimeLiteralArgument) {
            return RuleResult::fail($context->value());
        }

        return static::compare($currentTimestamp, $argument->timestamp())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }

    protected static function compare($currentTimestamp, $expectedTimestamp)
    {
        return false;
    }
}
