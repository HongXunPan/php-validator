<?php

namespace HongXunPan\Validator\Rule\Collection;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\IntRangeArgument;
use HongXunPan\Validator\Rule\Argument\IntRangeArgumentParser;
use HongXunPan\Validator\Rule\Marker\ListRule;

class ItemsBetweenRule extends AbstractPresentValueAssertionRule implements ListRule
{
    const KEY = 'itemsBetween';
    const MESSAGE = '$paramName item count must be between $rule';
    const ARGUMENT_PARSER = IntRangeArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $range = $context->parsedRuleArg();
        if (!is_array($context->value()) || !$range instanceof IntRangeArgument) {
            return RuleResult::fail($context->value());
        }

        $count = count($context->value());

        return $count >= $range->min() && $count <= $range->max()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
