<?php

namespace HongXunPan\Validator\Rule\Collection;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\ListRule;

class SortAscRule extends AbstractValueRule implements ListRule
{
    const KEY = 'sortAsc';
    const MESSAGE = '$paramName must be sortable list';

    public static function validate($context)
    {
        if (!is_array($context->value())) {
            return RuleResult::fail($context->value());
        }

        $value = $context->value();
        sort($value);

        return RuleResult::pass(array_values($value));
    }
}
