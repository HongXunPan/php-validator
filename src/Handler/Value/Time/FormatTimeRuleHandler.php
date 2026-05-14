<?php

namespace HongXunPan\Validator\Handler\Value\Time;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;
use HongXunPan\Validator\Rule\Marker\TimeRule;

class FormatTimeRuleHandler implements ValueRuleHandlerInterface, TimeRule
{
    public static function validate($context)
    {
        $timestamp = strtotime((string)$context->value());
        if ($timestamp === false) {
            return false;
        }

        return array(
            'passed' => true,
            'value' => date((string)$context->ruleArgument(), $timestamp),
        );
    }
}
