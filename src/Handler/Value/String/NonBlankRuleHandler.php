<?php

namespace HongXunPan\Validator\Handler\Value\String;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;
use HongXunPan\Validator\Rule\Marker\StringRule;

class NonBlankRuleHandler implements ValueRuleHandlerInterface, StringRule
{
    public static function validate($context)
    {
        $value = $context->value();
        $ruleName = $context->definition()->name()->value();

        if ($ruleName === 'notnull') {
            return static::validateNotNull($value);
        }

        if (!is_string($value)) {
            return false;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return false;
        }

        if ($ruleName === 'trimmedRequiredString') {
            return array(
                'passed' => true,
                'value' => $trimmed,
            );
        }

        return true;
    }

    private static function validateNotNull($value)
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_scalar($value)) {
            return trim((string)$value) !== '';
        }

        return false;
    }
}
