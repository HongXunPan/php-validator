<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractCrossFieldAssertionRule;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgument;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;
use HongXunPan\Validator\Rule\Concern\BuildsFieldReferenceRule;

class DifferentFieldRule extends AbstractCrossFieldAssertionRule
{
    use BuildsFieldReferenceRule;

    const KEY = 'differentField';
    const MESSAGE = '$paramName must be different from $rule';
    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $fieldPath = self::fieldPath($context->parsedRuleArg());
        $otherValue = $context->dependent($fieldPath);
        if (!$otherValue->exists()) {
            return RuleResult::pass($context->value());
        }

        return $context->value() !== $otherValue->value()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }

    public static function displayRuleValue($rawArg, PathLabelMap $pathLabelMap)
    {
        $fieldPath = self::fieldPath($rawArg);

        return $pathLabelMap->resolve($fieldPath, $fieldPath);
    }

    /**
     * @param mixed $argument
     *
     * @return string
     */
    private static function fieldPath($argument)
    {
        return $argument instanceof FieldReferenceArgument
            ? $argument->fieldPath()
            : (string)$argument;
    }
}
