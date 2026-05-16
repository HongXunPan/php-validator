<?php

namespace HongXunPan\Validator\Internal\Target;

use HongXunPan\Validator\Context\PathValue;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Plan\CompiledTargetRulePlan;
use HongXunPan\Validator\Internal\State\ValidationState;

class TargetLifecycleManager
{
    public function createTargetValueContext(PathValue $rawPathValue)
    {
        return new TargetValueContext($rawPathValue->exists(), $rawPathValue->value());
    }

    public function finalizeAfterMaterialization(TargetValueContext $targetValueContext)
    {
        $targetValueContext->useCurrentAsMaterialized();
    }

    public function finalizeAfterLocalRules(ValidationState $state, CompiledTargetRulePlan $targetPlan, TargetValueContext $targetValueContext)
    {
        $targetValueContext->markDependentReadable();
        $targetValueContext->commitOutputValue($state->normalizeOutput());

        if (!$targetPlan->hasDependentValueRules()) {
            $state->writeValidatedTarget($targetPlan->ruleTarget(), $targetValueContext);
        }
    }

    public function finalizeAfterDependentRules(ValidationState $state, CompiledTargetRulePlan $targetPlan, TargetValueContext $targetValueContext)
    {
        $targetValueContext->commitOutputValue($state->normalizeOutput());
        $state->writeValidatedTarget($targetPlan->ruleTarget(), $targetValueContext);
    }
}
