<?php

namespace HongXunPan\Validator\Rule\Collection;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\ListRule;

class MinItemsRule extends AbstractValueRule implements ListRule
{
    const KEY = 'minItems';
    const MESSAGE = '$paramName must contain at least $rule items';

    public static function validate($context)
    {
        if (!is_array($context->value())) {
            return RuleResult::fail($context->value());
        }

        return count($context->value()) >= (int)$context->ruleArg()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
