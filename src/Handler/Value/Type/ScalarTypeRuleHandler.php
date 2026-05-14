<?php

namespace HongXunPan\Validator\Handler\Value\Type;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class ScalarTypeRuleHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        $value = $context->value();
        $ruleName = $context->definition()->name()->value();

        switch ($ruleName) {
            case 'int':
                return $value == intval($value) && $value !== null;
            case 'array':
                return is_array($value);
            case 'string':
                return is_string($value);
            case 'time':
                return strtotime((string)$value) !== false;
        }

        return false;
    }
}
