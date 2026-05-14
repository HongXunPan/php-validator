<?php

namespace HongXunPan\Validator\Handler\Value\Compare;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class FieldComparisonRuleHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        $ruleName = $context->definition()->name()->value();
        if (in_array($ruleName, array('gtField', 'egtField', 'ltField', 'eltField'), true)) {
            return static::compareNumber($context, $ruleName);
        }

        return static::compareTime($context, $ruleName);
    }

    private static function compareNumber($context, $ruleName)
    {
        list($fieldPath) = static::parseFieldCompareArgument($context->ruleArgument());
        $otherValueInfo = $context->readPath($fieldPath);
        $value = $context->value();
        $otherValue = $otherValueInfo['exists'] ? $otherValueInfo['value'] : null;

        if (static::isBlankComparableValue($value) || static::isBlankComparableValue($otherValue)) {
            return true;
        }

        if (!is_numeric($value) || !is_numeric($otherValue)) {
            return false;
        }

        $number = (float)$value;
        $otherNumber = (float)$otherValue;

        switch ($ruleName) {
            case 'gtField':
                return $number > $otherNumber;
            case 'egtField':
                return $number >= $otherNumber;
            case 'ltField':
                return $number < $otherNumber;
            case 'eltField':
                return $number <= $otherNumber;
        }

        return false;
    }

    private static function compareTime($context, $ruleName)
    {
        list($fieldPath) = static::parseFieldCompareArgument($context->ruleArgument());
        $otherValueInfo = $context->readPath($fieldPath);
        $value = $context->value();
        $otherValue = $otherValueInfo['exists'] ? $otherValueInfo['value'] : null;

        if (static::isBlankComparableValue($value) || static::isBlankComparableValue($otherValue)) {
            return true;
        }

        $timestamp = strtotime((string)$value);
        $otherTimestamp = strtotime((string)$otherValue);
        if ($timestamp === false || $otherTimestamp === false) {
            return false;
        }

        switch ($ruleName) {
            case 'timeAfterField':
                return $timestamp > $otherTimestamp;
            case 'timeAfterOrEqualField':
                return $timestamp >= $otherTimestamp;
            case 'timeBeforeField':
                return $timestamp < $otherTimestamp;
            case 'timeBeforeOrEqualField':
                return $timestamp <= $otherTimestamp;
        }

        return false;
    }

    private static function parseFieldCompareArgument($argument)
    {
        $parts = explode(',', (string)$argument, 2);

        return array(
            isset($parts[0]) ? $parts[0] : '',
            isset($parts[1]) ? $parts[1] : (isset($parts[0]) ? $parts[0] : ''),
        );
    }

    private static function isBlankComparableValue($value)
    {
        return $value === null || $value === '';
    }
}
