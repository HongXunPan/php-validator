<?php

namespace HongXunPan\Validator\Handler\Value\Time;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;
use HongXunPan\Validator\Rule\Marker\TimeRule;

class TimeFormatRuleHandler implements ValueRuleHandlerInterface, TimeRule
{
    public static function validate($context)
    {
        $value = $context->value();
        $format = (string)$context->ruleArgument();
        $timestamp = strtotime((string)$value);

        return $timestamp !== false && date($format, $timestamp) === $value;
    }
}
