<?php

namespace HongXunPan\Validator\Internal\Execution;

use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Plan\CompiledRule;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Rule\ConditionalPresenceRuleInterface;

class PhaseRuleRunner
{
    /**
     * @var RuleExecutor
     */
    private $ruleExecutor;

    public function __construct(RuleExecutor $ruleExecutor)
    {
        $this->ruleExecutor = $ruleExecutor;
    }

    public function run(ValidationState $state, RuleTarget $ruleTarget, TargetValueContext $targetValueContext, array $compiledRules)
    {
        foreach ($compiledRules as $compiledRule) {
            if (!$compiledRule instanceof CompiledRule) {
                continue;
            }

            $outcome = $this->ruleExecutor->execute(
                $state,
                $ruleTarget,
                $compiledRule,
                $targetValueContext
            );

            if ($outcome->isSkipped()) {
                continue;
            }

            if ($outcome->isFailed()) {
                $targetValueContext->markFailed();
                $state->addTargetFailure(
                    $ruleTarget,
                    $compiledRule->parsedRule(),
                    $targetValueContext,
                    $outcome
                );

                return PhaseExecutionResult::failed();
            }

            $targetValueContext->applyRuleResult($outcome->ruleResult());
            if ($outcome->shouldBreak()) {
                $resolvedRule = $outcome->resolvedRule();
                if (
                    $resolvedRule !== null
                    && is_subclass_of($resolvedRule->ruleClass(), ConditionalPresenceRuleInterface::class)
                ) {
                    $targetValueContext->skipValueValidation();
                }

                return PhaseExecutionResult::broken();
            }
        }

        return PhaseExecutionResult::completed();
    }
}
