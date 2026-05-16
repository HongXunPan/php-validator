<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\DependentValueRuleInterface;
use HongXunPan\Validator\Rule\Marker\TimeRule;

abstract class AbstractTimeFieldCompareRule extends AbstractValueRule implements TimeRule, DependentValueRuleInterface
{
    public static function validate(RuleContext $context)
    {
        $fieldPath = static::parseFieldPath($context->ruleArg());
        $otherValueResult = $context->getDependentTargetValue($fieldPath);

        if (!$otherValueResult->exists()) {
            return RuleResult::pass($context->value());
        }

        $currentValue = $context->value();
        $otherValue = $otherValueResult->value();

        if (static::isBlankComparableValue($currentValue) || static::isBlankComparableValue($otherValue)) {
            return RuleResult::pass($currentValue);
        }

        $currentTimestamp = strtotime((string)$currentValue);
        $otherTimestamp = strtotime((string)$otherValue);
        if ($currentTimestamp === false || $otherTimestamp === false) {
            return RuleResult::fail($currentValue);
        }

        return static::compare($currentTimestamp, $otherTimestamp)
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
