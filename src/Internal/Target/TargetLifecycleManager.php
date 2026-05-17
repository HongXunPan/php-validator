<?php

namespace HongXunPan\Validator\Internal\Target;

use HongXunPan\Validator\Context\PathValue;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Plan\CompiledTargetRulePlan;
use HongXunPan\Validator\Internal\State\ValidationState;

class TargetLifecycleManager
{
    /**
     * @param PathValue $rawPathValue
     *
     * @return TargetValueContext
     */
    public function createTargetValueContext(PathValue $rawPathValue)
    {
        return new TargetValueContext($rawPathValue->exists(), $rawPathValue->value());
    }

    /**
     * @param TargetValueContext $targetValueContext
     *
     * @return void
     */
    public function finalizeAfterPreparation(TargetValueContext $targetValueContext)
    {
        $targetValueContext->useCurrentAsMaterialized();
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     * @param TargetValueContext $targetValueContext
     *
     * @return void
     */
    public function finalizeAfterPresentValueAssertions(ValidationState $state, CompiledTargetRulePlan $targetPlan, TargetValueContext $targetValueContext)
    {
        $targetValueContext->markDependentReadable();
        $targetValueContext->commitOutputValue($state->normalizeOutput());

        if (!$targetPlan->hasCrossFieldAssertionRules()) {
            $state->writeValidatedTarget($targetPlan->ruleTarget(), $targetValueContext);
        }
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     * @param TargetValueContext $targetValueContext
     *
     * @return void
     */
    public function finalizeAfterCrossFieldAssertions(ValidationState $state, CompiledTargetRulePlan $targetPlan, TargetValueContext $targetValueContext)
    {
        $targetValueContext->commitOutputValue($state->normalizeOutput());
        $state->writeValidatedTarget($targetPlan->ruleTarget(), $targetValueContext);
    }
}
