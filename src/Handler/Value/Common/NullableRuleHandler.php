<?php

namespace HongXunPan\Validator\Handler\Value\Common;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class NullableRuleHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        $value = $context->value();
        if ($value !== null) {
            return true;
        }

        $ruleName = $context->definition()->name()->value();
        if ($ruleName === 'nullable') {
            return array(
                'passed' => true,
                'value' => null,
                'break' => true,
            );
        }

        list($field, $expectedValue) = static::parseFieldValueArgument($context->ruleArgument());
        $otherValueInfo = $context->readPath($field);
        if (!$otherValueInfo['exists'] || $otherValueInfo['value'] != $expectedValue) {
            return true;
        }

        return array(
            'passed' => true,
            'value' => null,
            'break' => true,
        );
    }

    private static function parseFieldValueArgument($argument)
    {
        $parts = explode(',', (string)$argument, 2);

        return array(
            isset($parts[0]) ? $parts[0] : '',
            isset($parts[1]) ? $parts[1] : '',
        );
    }
}
