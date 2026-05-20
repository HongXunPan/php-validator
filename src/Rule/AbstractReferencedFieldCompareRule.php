<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgument;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;
use HongXunPan\Validator\Rule\Concern\BuildsFieldReferenceRule;

abstract class AbstractReferencedFieldCompareRule extends AbstractCrossFieldAssertionRule
{
    use BuildsFieldReferenceRule;

    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $referencedValue = $context->dependent(static::parseFieldPath($context->parsedRuleArg()));
        if (!$referencedValue->exists()) {
            return RuleResult::pass($context->value());
        }

        $currentValue = $context->value();
        $otherValue = $referencedValue->value();

        if (static::isBlankComparableValue($currentValue) || static::isBlankComparableValue($otherValue)) {
            return RuleResult::pass($currentValue);
        }

        $normalizedPair = static::normalizeComparablePair($currentValue, $otherValue);
        if ($normalizedPair === null) {
            return RuleResult::fail($currentValue);
        }

        return static::compare($normalizedPair[0], $normalizedPair[1])
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

    /**
     * @param mixed $currentValue
     * @param mixed $otherValue
     *
     * @return array<int, mixed>|null
     */
    protected static function normalizeComparablePair($currentValue, $otherValue)
    {
        return null;
    }

    protected static function compare($currentValue, $otherValue)
    {
        return false;
    }
}
