<?php

namespace HongXunPan\Validator\Internal\Execution;

use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Plan\CompiledRule;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Support\RuleResultNormalizer;

class RuleExecutor
{
    /**
     * @var RuleSet
     */
    private $ruleSet;
    /**
     * @var RuleContextFactory
     */
    private $ruleContextFactory;
    /**
     * @var RuleResultNormalizer
     */
    private $ruleResultNormalizer;

    /**
     * @param RuleSet $ruleSet
     * @param LiteralValueParser $literalValueParser
     * @param RuleResultNormalizer $ruleResultNormalizer
     */
    public function __construct(
        RuleSet $ruleSet,
        LiteralValueParser $literalValueParser,
        RuleResultNormalizer $ruleResultNormalizer
    ) {
        $this->ruleSet = $ruleSet;
        $this->ruleContextFactory = new RuleContextFactory($literalValueParser);
        $this->ruleResultNormalizer = $ruleResultNormalizer;
    }

    /**
     * @param ValidationState $state
     * @param RuleTarget $ruleTarget
     * @param CompiledRule $compiledRule
     * @param TargetValueContext $targetValueContext
     *
     * @return RuleExecutionOutcome
     */
    public function execute(ValidationState $state, RuleTarget $ruleTarget, CompiledRule $compiledRule, TargetValueContext $targetValueContext)
    {
        if ($compiledRule->isUnsupported()) {
            return RuleExecutionOutcome::unsupported();
        }

        $resolvedRule = $compiledRule->resolvedRule();
        $ruleClass = $resolvedRule->ruleClass();
        $rawResult = call_user_func(
            array($ruleClass, 'validate'),
            $this->ruleContextFactory->create(
                $ruleTarget,
                $state->displayName($ruleTarget),
                $compiledRule,
                $targetValueContext,
                $state->targetValueReader()
            )
        );

        $ruleResult = $this->ruleResultNormalizer->normalize($rawResult, $targetValueContext->currentValue());
        if ($ruleResult->failed()) {
            return RuleExecutionOutcome::failed(
                $resolvedRule,
                $this->ruleSet->resolveMessage($resolvedRule),
                $ruleResult
            );
        }

        return RuleExecutionOutcome::passed($resolvedRule, $ruleResult);
    }
}
