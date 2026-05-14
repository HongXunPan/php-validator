<?php

namespace HongXunPan\Validator\Handler\Presence;

use HongXunPan\Validator\Handler\PresenceRuleHandlerInterface;

class ConditionalPresenceRuleHandler implements PresenceRuleHandlerInterface
{
    public static function validate($context)
    {
        $ruleName = $context->definition()->name()->value();

        switch ($ruleName) {
            case 'requiredIf':
                return static::validateRequiredIf($context);
            case 'requiredWithout':
                return static::validateRequiredWithout($context);
            case 'prohibitedWith':
                return static::validateProhibitedWith($context);
            case 'prohibitedUnless':
                return static::validateProhibitedUnless($context);
        }

        return true;
    }

    private static function validateRequiredIf($context)
    {
        if ($context->exists()) {
            return true;
        }

        list($field, $expectedValue) = static::parseFieldValueArgument($context->ruleArgument());
        $otherValueInfo = $context->readPath($field);
        if (!$otherValueInfo['exists']) {
            return true;
        }

        return $otherValueInfo['value'] != $expectedValue;
    }

    private static function validateRequiredWithout($context)
    {
        if ($context->exists()) {
            return true;
        }

        $otherValueInfo = $context->readPath($context->ruleArgument());

        return $otherValueInfo['exists'];
    }

    private static function validateProhibitedWith($context)
    {
        if (!$context->exists()) {
            return true;
        }

        $otherValueInfo = $context->readPath($context->ruleArgument());

        return !$otherValueInfo['exists'];
    }

    private static function validateProhibitedUnless($context)
    {
        if (!$context->exists()) {
            return true;
        }

        list($field, $expectedValue) = static::parseFieldValueArgument($context->ruleArgument());
        $otherValueInfo = $context->readPath($field);
        if (!$otherValueInfo['exists']) {
            return false;
        }

        return $otherValueInfo['value'] == $expectedValue;
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
