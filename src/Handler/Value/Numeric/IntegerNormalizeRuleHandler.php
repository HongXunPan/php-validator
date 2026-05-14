<?php

namespace HongXunPan\Validator\Handler\Value\Numeric;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;
use HongXunPan\Validator\Rule\Marker\NumericRule;

class IntegerNormalizeRuleHandler implements ValueRuleHandlerInterface, NumericRule
{
    public static function validate($context)
    {
        $value = $context->value();
        if (!($value == intval($value) && $value !== null)) {
            return false;
        }

        $normalized = (int)$value;
        $ruleName = $context->definition()->name()->value();

        if ($ruleName === 'positiveInt') {
            return array(
                'passed' => $normalized > 0,
                'value' => $normalized,
            );
        }

        return array(
            'passed' => $normalized >= 0,
            'value' => $normalized,
        );
    }
}
