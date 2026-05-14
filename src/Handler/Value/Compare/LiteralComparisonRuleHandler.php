<?php

namespace HongXunPan\Validator\Handler\Value\Compare;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class LiteralComparisonRuleHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        $value = $context->value();
        $expect = $context->ruleArgument();
        $ruleName = $context->definition()->name()->value();

        switch ($ruleName) {
            case 'eq':
                return $value == $expect;
            case 'neq':
                return $value != $expect;
            case 'gt':
                return $value > $expect;
            case 'egt':
            case 'gte':
                return $value >= $expect;
            case 'lt':
                return $value < $expect;
            case 'elt':
            case 'lte':
                return $value <= $expect;
            case 'in':
                $allowedValues = json_decode((string)$expect, true);

                return is_array($allowedValues) && in_array($value, $allowedValues);
        }

        return false;
    }
}
