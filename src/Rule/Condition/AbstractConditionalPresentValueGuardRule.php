<?php

namespace HongXunPan\Validator\Rule\Condition;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgument;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgument;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgument;
use HongXunPan\Validator\Rule\AbstractPresentValueGuardRule;
use LogicException;

/**
 * 条件守卫规则扩展基类。
 * 供引用其他字段作为触发条件的 present value guard rule 复用前置判断。
 */
abstract class AbstractConditionalPresentValueGuardRule extends AbstractPresentValueGuardRule
{
    /**
     * @return RuleResult
     */
    protected static function passCurrent(RuleContext $context)
    {
        return RuleResult::pass($context->value(), $context->fieldExists());
    }

    /**
     * @return RuleResult|null
     */
    protected static function skipUnlessReferencedEq(RuleContext $context)
    {
        $argument = static::expectedLiteralArgument($context);
        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());

        if (!$otherValue->exists() || !ConditionValueMatcher::eq($otherValue->value(), $argument->expectedValue())) {
            return static::passCurrent($context);
        }

        return null;
    }

    /**
     * @return RuleResult|null
     */
    protected static function skipUnlessReferencedIn(RuleContext $context)
    {
        $argument = static::expectedLiteralSetArgument($context);
        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());

        if (!$otherValue->exists() || !ConditionValueMatcher::in($otherValue->value(), $argument->expectedValues())) {
            return static::passCurrent($context);
        }

        return null;
    }

    /**
     * @return RuleResult|null
     */
    protected static function skipUnlessReferencedNotEq(RuleContext $context)
    {
        $argument = static::expectedLiteralArgument($context);
        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());

        if (!$otherValue->exists() || !ConditionValueMatcher::notEq($otherValue->value(), $argument->expectedValue())) {
            return static::passCurrent($context);
        }

        return null;
    }

    /**
     * @return RuleResult|null
     */
    protected static function skipUnlessReferencedNotIn(RuleContext $context)
    {
        $argument = static::expectedLiteralSetArgument($context);
        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());

        if (!$otherValue->exists() || !ConditionValueMatcher::notIn($otherValue->value(), $argument->expectedValues())) {
            return static::passCurrent($context);
        }

        return null;
    }

    /**
     * @return RuleResult|null
     */
    protected static function skipUnlessReferencedPresent(RuleContext $context)
    {
        $argument = static::fieldReferenceArgument($context);
        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());

        if (!$otherValue->exists()) {
            return static::passCurrent($context);
        }

        return null;
    }

    /**
     * @return RuleResult|null
     */
    protected static function skipUnlessReferencedMissing(RuleContext $context)
    {
        $argument = static::fieldReferenceArgument($context);
        $otherValue = $context->getMaterializedTargetValue($argument->fieldPath());

        if ($otherValue->exists()) {
            return static::passCurrent($context);
        }

        return null;
    }

    /**
     * @return FieldExpectedLiteralArgument
     */
    protected static function expectedLiteralArgument(RuleContext $context)
    {
        $argument = $context->parsedRuleArg();
        if (!$argument instanceof FieldExpectedLiteralArgument) {
            throw new LogicException(static::key() . ' 解析参数类型异常');
        }

        return $argument;
    }

    /**
     * @return FieldExpectedLiteralSetArgument
     */
    protected static function expectedLiteralSetArgument(RuleContext $context)
    {
        $argument = $context->parsedRuleArg();
        if (!$argument instanceof FieldExpectedLiteralSetArgument) {
            throw new LogicException(static::key() . ' 解析参数类型异常');
        }

        return $argument;
    }

    /**
     * @return FieldReferenceArgument
     */
    protected static function fieldReferenceArgument(RuleContext $context)
    {
        $argument = $context->parsedRuleArg();
        if (!$argument instanceof FieldReferenceArgument) {
            throw new LogicException(static::key() . ' 解析参数类型异常');
        }

        return $argument;
    }
}
