<?php

namespace HongXunPan\Validator\Rule\Collection;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\ListRule;

class MaxItemsRule extends AbstractValueRule implements ListRule
{
    const KEY = 'maxItems';
    const MESSAGE = '$paramName must contain at most $rule items';

    public static function validate($context)
    {
        if (!is_array($context->value())) {
            return RuleResult::fail($context->value());
        }

        return count($context->value()) <= (int)$context->ruleArg()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
