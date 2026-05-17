<?php

namespace HongXunPan\Validator\Rule\Transform\Time;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueTransformRule;
use HongXunPan\Validator\Rule\Argument\FormatStringArgument;
use HongXunPan\Validator\Rule\Argument\FormatStringArgumentParser;
use HongXunPan\Validator\Rule\Marker\TimeRule;

class FormatTimeRule extends AbstractPresentValueTransformRule implements TimeRule
{
    const KEY = 'formatTime';
    const MESSAGE = '$paramName must be time';
    const ARGUMENT_PARSER = FormatStringArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $timestamp = strtotime((string)$context->value());
        if ($timestamp === false) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass(date(self::format($context->parsedRuleArg()), $timestamp));
    }

    /**
     * @param mixed $ruleArg
     *
     * @return string
     */
    private static function format($ruleArg)
    {
        if ($ruleArg instanceof FormatStringArgument) {
            return $ruleArg->format();
        }

        return (string)$ruleArg;
    }
}
