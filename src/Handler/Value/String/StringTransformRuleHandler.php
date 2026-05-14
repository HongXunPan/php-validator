<?php

namespace HongXunPan\Validator\Handler\Value\String;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;
use HongXunPan\Validator\Rule\Marker\StringRule;

class StringTransformRuleHandler implements ValueRuleHandlerInterface, StringRule
{
    public static function validate($context)
    {
        $value = $context->value();
        $ruleName = $context->definition()->name()->value();

        if ($ruleName === 'blankToNull' && $value === null) {
            return array(
                'passed' => true,
                'value' => null,
            );
        }

        if (!is_string($value)) {
            return false;
        }

        $trimmed = trim($value);

        switch ($ruleName) {
            case 'trim':
            case 'trimmedString':
                return array(
                    'passed' => true,
                    'value' => $trimmed,
                );
            case 'blankToNull':
                return array(
                    'passed' => true,
                    'value' => $trimmed === '' ? null : $trimmed,
                );
        }

        return false;
    }
}
