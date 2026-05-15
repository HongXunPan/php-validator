<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Internal\Field\PathLabelMap;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\NumericRule;

abstract class AbstractNumericFieldCompareRule extends AbstractValueRule implements NumericRule
{
    public static function validate($context)
    {
        $fieldPath = static::parseFieldPath($context->ruleArg());
        $otherValueResult = $context->getFieldValue($fieldPath, true);

        if (!$otherValueResult->exists()) {
            return RuleResult::fail($context->value());
        }

        $currentValue = $context->value();
        $otherValue = $otherValueResult->value();

        if (static::isBlankComparableValue($currentValue) || static::isBlankComparableValue($otherValue)) {
            return RuleResult::pass($currentValue);
        }

        if (!is_numeric($currentValue) || !is_numeric($otherValue)) {
            return RuleResult::fail($currentValue);
        }

        return static::compare((float)$currentValue, (float)$otherValue)
            ? RuleResult::pass($currentValue)
            : RuleResult::fail($currentValue);
    }

    protected static function parseFieldPath($ruleArg)
    {
        return (string)$ruleArg;
    }

    public static function displayRuleValue($rawArg, PathLabelMap $pathLabelMap)
    {
        $fieldPath = static::parseFieldPath($rawArg);

        return $pathLabelMap->resolve($fieldPath, $fieldPath);
    }

    protected static function isBlankComparableValue($value)
    {
        return $value === null || $value === '';
    }

    protected static function compare($currentValue, $otherValue)
    {
        return false;
    }
}
