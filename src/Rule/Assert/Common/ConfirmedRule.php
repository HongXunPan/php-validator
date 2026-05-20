<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractCrossFieldAssertionRule;
use HongXunPan\Validator\Rule\Argument\ConfirmedFieldArgument;
use HongXunPan\Validator\Rule\Argument\ConfirmedFieldArgumentParser;
use HongXunPan\Validator\Rule\Concern\BuildsFieldReferenceRule;

class ConfirmedRule extends AbstractCrossFieldAssertionRule
{
    use BuildsFieldReferenceRule;

    const KEY = 'confirmed';
    const MESSAGE = '$paramName confirmation does not match';
    const ARGUMENT_PARSER = ConfirmedFieldArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $fieldPath = self::confirmationFieldPath($context);
        $otherValue = $context->dependent($fieldPath);
        if (!$otherValue->exists()) {
            return RuleResult::fail($context->value());
        }

        return $context->value() === $otherValue->value()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }

    /**
     * @return string
     */
    private static function confirmationFieldPath(RuleContext $context)
    {
        $argument = $context->parsedRuleArg();
        if ($argument instanceof ConfirmedFieldArgument && $argument->fieldPath() !== null) {
            return $argument->fieldPath();
        }

        return $context->fieldPath() . '_confirmation';
    }
}
