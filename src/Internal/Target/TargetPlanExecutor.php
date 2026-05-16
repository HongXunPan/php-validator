<?php

namespace HongXunPan\Validator\Internal\Target;

use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Execution\PhaseRuleRunner;
use HongXunPan\Validator\Internal\Plan\CompiledTargetRulePlan;
use HongXunPan\Validator\Internal\State\ValidationState;

class TargetPlanExecutor
{
    /**
     * @var PhaseRuleRunner
     */
    private $phaseRuleRunner;
    /**
     * @var TargetLifecycleManager
     */
    private $targetLifecycleManager;

    public function __construct(PhaseRuleRunner $phaseRuleRunner, TargetLifecycleManager $targetLifecycleManager)
    {
        $this->phaseRuleRunner = $phaseRuleRunner;
        $this->targetLifecycleManager = $targetLifecycleManager;
    }

    public function materialize(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $ruleTarget = $targetPlan->ruleTarget();
        $rawPathValue = $state->targetValueReader()->rawPathValue($ruleTarget->fieldPath(), $state->strict());
        $targetValueContext = $this->targetLifecycleManager->createTargetValueContext($rawPathValue);

        $state->rememberTargetValueContext($ruleTarget->fieldPath(), $targetValueContext);

        if ($targetPlan->hasUnsupportedRules()) {
            $unsupportedResult = $this->phaseRuleRunner->run(
                $state,
                $ruleTarget,
                $targetValueContext,
                $targetPlan->unsupportedRules()
            );
            if ($unsupportedResult->isFailed()) {
                return;
            }
        }

        $materializationResult = $this->phaseRuleRunner->run(
            $state,
            $ruleTarget,
            $targetValueContext,
            $targetPlan->materializationRules()
        );
        if ($materializationResult->isFailed()) {
            return;
        }

        $this->targetLifecycleManager->finalizeAfterMaterialization($targetValueContext);
    }

    public function validateConditionalPresence(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->conditionalPresenceRules()
        );
    }

    public function validatePresence(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->presenceRules()
        );
    }

    public function validateLocalValue(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        if ($targetValueContext->shouldSkipValueValidation()) {
            $this->targetLifecycleManager->finalizeAfterLocalRules($state, $targetPlan, $targetValueContext);

            return;
        }

        if (!$targetValueContext->currentExists()) {
            return;
        }

        $localValueResult = $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->localValueRules()
        );
        if ($localValueResult->isFailed()) {
            return;
        }

        $this->targetLifecycleManager->finalizeAfterLocalRules($state, $targetPlan, $targetValueContext);
    }

    public function validateDependentValue(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->dependentReadableTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        if (!$targetPlan->hasDependentValueRules()) {
            return;
        }

        $dependentValueResult = $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->dependentValueRules()
        );
        if ($dependentValueResult->isFailed()) {
            return;
        }

        $this->targetLifecycleManager->finalizeAfterDependentRules($state, $targetPlan, $targetValueContext);
    }

    private function activeTargetValueContext(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $state->targetValueContextStore()->get($targetPlan->ruleTarget()->fieldPath());
        if (!$targetValueContext instanceof TargetValueContext || !$targetValueContext->isMaterialized() || $targetValueContext->isFailed()) {
            return null;
        }

        return $targetValueContext;
    }

    private function dependentReadableTargetValueContext(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContext($state, $targetPlan);
        if (
            !$targetValueContext instanceof TargetValueContext
            || !$targetValueContext->isDependentReadable()
            || $targetValueContext->shouldSkipValueValidation()
        ) {
            return null;
        }

        return $targetValueContext;
    }
}
