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

    /**
     * @param PhaseRuleRunner $phaseRuleRunner
     * @param TargetLifecycleManager $targetLifecycleManager
     */
    public function __construct(PhaseRuleRunner $phaseRuleRunner, TargetLifecycleManager $targetLifecycleManager)
    {
        $this->phaseRuleRunner = $phaseRuleRunner;
        $this->targetLifecycleManager = $targetLifecycleManager;
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return void
     */
    public function prepareMissingValue(ValidationState $state, CompiledTargetRulePlan $targetPlan)
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

        $missingValueCreationResult = $this->phaseRuleRunner->run(
            $state,
            $ruleTarget,
            $targetValueContext,
            $targetPlan->missingValueCreationRules()
        );
        if ($missingValueCreationResult->isFailed()) {
            return;
        }
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return void
     */
    public function preparePresentValue(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContextWithoutPreparation($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        if ($targetValueContext->currentExists()) {
            $normalizationResult = $this->phaseRuleRunner->run(
                $state,
                $targetPlan->ruleTarget(),
                $targetValueContext,
                $targetPlan->presentValueNormalizationRules()
            );
            if ($normalizationResult->isFailed()) {
                return;
            }
        }

        $this->targetLifecycleManager->finalizeAfterPreparation($targetValueContext);
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return void
     */
    public function assertFieldPresence(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->fieldPresenceAssertionRules()
        );
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return void
     */
    public function guardPresentValue(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        if (!$targetValueContext->currentExists()) {
            return;
        }

        $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->presentValueGuardRules()
        );
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return void
     */
    public function transformPresentValue(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        if ($targetValueContext->shouldSkipValueValidation()) {
            return;
        }

        if (!$targetValueContext->currentExists()) {
            return;
        }

        $presentValueTransformResult = $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->presentValueTransformRules()
        );
        if ($presentValueTransformResult->isFailed()) {
            return;
        }
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return void
     */
    public function assertPresentValue(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->activeTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        if ($targetValueContext->shouldSkipValueValidation()) {
            $this->targetLifecycleManager->finalizeAfterPresentValueAssertions($state, $targetPlan, $targetValueContext);

            return;
        }

        if (!$targetValueContext->currentExists()) {
            return;
        }

        $presentValueAssertionResult = $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->presentValueAssertionRules()
        );
        if ($presentValueAssertionResult->isFailed()) {
            return;
        }

        $this->targetLifecycleManager->finalizeAfterPresentValueAssertions($state, $targetPlan, $targetValueContext);
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return void
     */
    public function assertCrossFieldValue(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $this->dependentReadableTargetValueContext($state, $targetPlan);
        if (!$targetValueContext instanceof TargetValueContext) {
            return;
        }

        if (!$targetPlan->hasCrossFieldAssertionRules()) {
            return;
        }

        $crossFieldAssertionResult = $this->phaseRuleRunner->run(
            $state,
            $targetPlan->ruleTarget(),
            $targetValueContext,
            $targetPlan->crossFieldAssertionRules()
        );
        if ($crossFieldAssertionResult->isFailed()) {
            return;
        }

        $this->targetLifecycleManager->finalizeAfterCrossFieldAssertions($state, $targetPlan, $targetValueContext);
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return TargetValueContext|null
     */
    private function activeTargetValueContext(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $state->targetValueContextStore()->get($targetPlan->ruleTarget()->fieldPath());
        if (!$targetValueContext instanceof TargetValueContext || !$targetValueContext->isMaterialized() || $targetValueContext->isFailed()) {
            return null;
        }

        return $targetValueContext;
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return TargetValueContext|null
     */
    private function activeTargetValueContextWithoutPreparation(ValidationState $state, CompiledTargetRulePlan $targetPlan)
    {
        $targetValueContext = $state->targetValueContextStore()->get($targetPlan->ruleTarget()->fieldPath());
        if (!$targetValueContext instanceof TargetValueContext || $targetValueContext->isFailed()) {
            return null;
        }

        return $targetValueContext;
    }

    /**
     * @param ValidationState $state
     * @param CompiledTargetRulePlan $targetPlan
     *
     * @return TargetValueContext|null
     */
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
