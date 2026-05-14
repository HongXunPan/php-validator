<?php

namespace HongXunPan\Validator\Handler\Value\Collection;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class ConditionalArrayRuleHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        list($fieldPath, $expectedValue) = static::parseFieldValueArgument($context->ruleArgument());
        $otherValueInfo = $context->readPath($fieldPath);
        if (!$otherValueInfo['exists'] || $otherValueInfo['value'] != $expectedValue) {
            return true;
        }

        $value = $context->value();
        if (!is_array($value)) {
            return false;
        }

        if ($context->definition()->name()->value() === 'emptyArrayIf') {
            return count($value) === 0;
        }

        return count($value) > 0;
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
