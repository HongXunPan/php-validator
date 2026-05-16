<?php

namespace HongXunPan\Validator\Rule\Collection;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\ListRule;

class DistinctRule extends AbstractValueRule implements ListRule
{
    const KEY = 'distinct';
    const MESSAGE = '$paramName must contain distinct items';

    public static function validate(RuleContext $context)
    {
        if (!is_array($context->value())) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass(array_values(array_unique($context->value(), SORT_REGULAR)));
    }
}
