<?php

namespace HongXunPan\Validator\Handler\Value\String;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;
use HongXunPan\Validator\Rule\Marker\StringRule;

class StringLengthRuleHandler implements ValueRuleHandlerInterface, StringRule
{
    public static function validate($context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return false;
        }

        $ruleName = $context->definition()->name()->value();
        $length = iconv_strlen($value);
        $argument = (string)$context->ruleArgument();

        switch ($ruleName) {
            case 'len':
                $parts = explode('-', $argument, 2);
                if (count($parts) !== 2) {
                    return false;
                }

                return $length >= (int)$parts[0] && $length <= (int)$parts[1];
            case 'lenMin':
            case 'minLength':
                return $length >= (int)$argument;
            case 'lenMax':
            case 'maxLength':
                return $length <= (int)$argument;
        }

        return false;
    }
}
