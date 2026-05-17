<?php

namespace HongXunPan\Validator\Rule\Collection;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueTransformRule;
use HongXunPan\Validator\Rule\Marker\ListRule;

class SortAscRule extends AbstractPresentValueTransformRule implements ListRule
{
    const KEY = 'sortAsc';
    const MESSAGE = '$paramName must be sortable list';

    public static function validate(RuleContext $context)
    {
        if (!is_array($context->value())) {
            return RuleResult::fail($context->value());
        }

        $value = $context->value();
        sort($value);

        return RuleResult::pass(array_values($value));
    }
}
