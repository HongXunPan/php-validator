<?php

namespace HongXunPan\Validator\Internal\Execution;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Internal\Field\RuleTarget;
use HongXunPan\Validator\Internal\Field\TargetState;
use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Rule\PresenceRuleInterface;
use HongXunPan\Validator\Rule\ValueRuleInterface;
use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Support\PathAccessor;
use HongXunPan\Validator\Support\RuleResultNormalizer;

class RuleExecutor
{
    /**
     * @var RuleSet
     */
    private $catalog;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var LiteralValueParser
     */
    private $literalValueParser;
    /**
     * @var RuleResultNormalizer
     */
    private $ruleResultNormalizer;

    public function __construct(
        RuleSet $ruleSet,
        PathAccessor $pathAccessor,
        LiteralValueParser $literalValueParser,
        RuleResultNormalizer $ruleResultNormalizer
    ) {
        $this->catalog = $ruleSet;
        $this->pathAccessor = $pathAccessor;
        $this->literalValueParser = $literalValueParser;
        $this->ruleResultNormalizer = $ruleResultNormalizer;
    }

    public function execute($phase, ValidationState $state, RuleTarget $ruleTarget, ParsedRuleToken $parsedRule, TargetState $targetState)
    {
        $resolvedRule = $this->catalog->resolveRule($parsedRule->inputRuleKey());
        if ($resolvedRule === null) {
            return RuleExecutionOutcome::unsupported();
        }

        $ruleClass = $resolvedRule->ruleClass();
        if (!$this->matchesPhase($ruleClass, $phase)) {
            return RuleExecutionOutcome::skipped();
        }

        $rawResult = call_user_func(
            array($ruleClass, 'validate'),
            new RuleContext(
                $ruleTarget->fieldPath(),
                $state->displayName($ruleTarget),
                $targetState->exists(),
                $targetState->value(),
                $parsedRule->rawArgument(),
                $state->rawData(),
                $this->pathAccessor,
                $this->literalValueParser
            )
        );

        $ruleResult = $this->ruleResultNormalizer->normalize($rawResult, $targetState->value());
        if ($ruleResult->failed()) {
            return RuleExecutionOutcome::failed(
                $resolvedRule,
                $this->catalog->resolveMessage($resolvedRule),
                $ruleResult
            );
        }

        return RuleExecutionOutcome::passed($resolvedRule, $ruleResult);
    }

    private function matchesPhase($ruleClass, $phase)
    {
        if ($phase === ValidationPhase::PRESENCE) {
            return is_subclass_of($ruleClass, PresenceRuleInterface::class);
        }

        if ($phase === ValidationPhase::VALUE) {
            return is_subclass_of($ruleClass, ValueRuleInterface::class);
        }

        return false;
    }
}
