<?php

namespace HongXunPan\Validator\Rule\Collection;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\IntArgument;
use HongXunPan\Validator\Rule\Argument\IntArgumentParser;
use HongXunPan\Validator\Rule\Marker\ListRule;

class MinItemsRule extends AbstractPresentValueAssertionRule implements ListRule
{
    const KEY = 'minItems';
    const MESSAGE = '$paramName must contain at least $rule items';
    const ARGUMENT_PARSER = IntArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if (!is_array($context->value())) {
            return RuleResult::fail($context->value());
        }

        return count($context->value()) >= self::intValue($context->parsedRuleArg())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }

    /**
     * @param mixed $ruleArg
     *
     * @return int
     */
    private static function intValue($ruleArg)
    {
        return $ruleArg instanceof IntArgument
            ? $ruleArg->value()
            : (int)$ruleArg;
    }
}
