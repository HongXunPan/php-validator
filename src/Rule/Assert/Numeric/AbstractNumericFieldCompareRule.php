<?php

namespace HongXunPan\Validator\Rule\Assert\Numeric;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractCrossFieldAssertionRule;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgument;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;
use HongXunPan\Validator\Rule\Marker\NumericRule;

abstract class AbstractNumericFieldCompareRule extends AbstractCrossFieldAssertionRule implements NumericRule
{
    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $fieldPath = static::parseFieldPath($context->parsedRuleArg());
        $otherValueResult = $context->getDependentTargetValue($fieldPath);

        if (!$otherValueResult->exists()) {
            return RuleResult::pass($context->value());
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
        if ($ruleArg instanceof FieldReferenceArgument) {
            return $ruleArg->fieldPath();
        }

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
