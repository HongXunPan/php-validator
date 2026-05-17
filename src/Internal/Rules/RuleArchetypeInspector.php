<?php

namespace HongXunPan\Validator\Internal\Rules;

use HongXunPan\Validator\Internal\Plan\CompiledRule;
use HongXunPan\Validator\Rule\AbstractCrossFieldAssertionRule;
use HongXunPan\Validator\Rule\AbstractFieldPresenceAssertionRule;
use HongXunPan\Validator\Rule\AbstractMissingValueCreationRule;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\AbstractPresentValueGuardRule;
use HongXunPan\Validator\Rule\AbstractPresentValueNormalizationRule;
use HongXunPan\Validator\Rule\AbstractPresentValueTransformRule;

class RuleArchetypeInspector
{
    /**
     * @param string $ruleClass
     *
     * @return string|null
     */
    public static function resolveCompiledStage($ruleClass)
    {
        if (is_subclass_of($ruleClass, AbstractMissingValueCreationRule::class)) {
            return CompiledRule::STAGE_PREPARE_MISSING_VALUE;
        }

        if (is_subclass_of($ruleClass, AbstractPresentValueNormalizationRule::class)) {
            return CompiledRule::STAGE_PREPARE_PRESENT_VALUE;
        }

        if (is_subclass_of($ruleClass, AbstractFieldPresenceAssertionRule::class)) {
            return CompiledRule::STAGE_ASSERT_FIELD_PRESENCE;
        }

        if (is_subclass_of($ruleClass, AbstractPresentValueGuardRule::class)) {
            return CompiledRule::STAGE_GUARD_PRESENT_VALUE;
        }

        if (is_subclass_of($ruleClass, AbstractPresentValueTransformRule::class)) {
            return CompiledRule::STAGE_TRANSFORM_PRESENT_VALUE;
        }

        if (is_subclass_of($ruleClass, AbstractPresentValueAssertionRule::class)) {
            return CompiledRule::STAGE_ASSERT_PRESENT_VALUE;
        }

        if (is_subclass_of($ruleClass, AbstractCrossFieldAssertionRule::class)) {
            return CompiledRule::STAGE_ASSERT_CROSS_FIELD_VALUE;
        }

        return null;
    }

    /**
     * @param string $ruleClass
     *
     * @return bool
     */
    public static function isPresentValueGuardRule($ruleClass)
    {
        return is_subclass_of($ruleClass, AbstractPresentValueGuardRule::class);
    }
}
