<?php

namespace HongXunPan\Validator\Internal\Target;

use HongXunPan\Validator\Internal\Execution\RuleExecutor;
use HongXunPan\Validator\Internal\Execution\ValidationPhase;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Rule\ConditionalPresenceRuleInterface;
use HongXunPan\Validator\Rule\DependentValueRuleInterface;
use HongXunPan\Validator\Rule\PresenceRuleInterface;
use HongXunPan\Validator\Rule\ValueMaterializationRuleInterface;
use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Support\RuleResultNormalizer;

class TargetValidator
{
    /**
     * @var RuleSet
     */
    private $ruleSet;
    /**
     * @var RuleStringParser
     */
    private $ruleStringParser;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var RuleExecutor
     */
    private $ruleExecutor;

    public function __construct(RuleSet $ruleSet, RuleStringParser $ruleStringParser, PathAccessor $pathAccessor)
    {
        $this->ruleSet = $ruleSet;
        $this->ruleStringParser = $ruleStringParser;
        $this->pathAccessor = $pathAccessor;
        $this->ruleExecutor = new RuleExecutor(
            $ruleSet,
            $pathAccessor,
            new LiteralValueParser(),
            new RuleResultNormalizer()
        );
    }

    public function materialize(ValidationState $state, $rawFieldKey, $ruleString)
    {
        $ruleTarget = $this->ruleStringParser->parseTargetKey($rawFieldKey);
        $ruleItems = $this->ruleStringParser->parseRuleItems($ruleString);
        $lookupResult = $this->pathAccessor->getValue(
            $state->rawData(),
            $ruleTarget->fieldPath(),
            $state->strict()
        );
        $targetValueContext = new TargetValueContext($lookupResult->exists(), $lookupResult->value());

        $state->rememberTargetValueContext($ruleTarget->fieldPath(), $targetValueContext);

        if (!$this->runMaterializationRules($state, $ruleTarget, $ruleItems, $targetValueContext)) {
            return;
        }

        $targetValueContext->useCurrentAsMaterialized();
    }

    public function validateConditionalPresence(ValidationState $state, $rawFieldKey, $ruleString)
    {
        $ruleTarget = $this->ruleStringParser->parseTargetKey($rawFieldKey);
        $targetValueContext = $state->targetValueContextStore()->get($ruleTarget->fieldPath());
        if (!$targetValueContext instanceof TargetValueContext || !$targetValueContext->isMaterialized() || $targetValueContext->isFailed()) {
            return;
        }

        $ruleItems = $this->ruleStringParser->parseRuleItems($ruleString);

        $this->runConditionalPresenceRules($state, $ruleTarget, $ruleItems, $targetValueContext);
    }

    public function validatePresence(ValidationState $state, $rawFieldKey, $ruleString)
    {
        $ruleTarget = $this->ruleStringParser->parseTargetKey($rawFieldKey);
        $targetValueContext = $state->targetValueContextStore()->get($ruleTarget->fieldPath());
        if (!$targetValueContext instanceof TargetValueContext || !$targetValueContext->isMaterialized() || $targetValueContext->isFailed()) {
            return;
        }

        $ruleItems = $this->ruleStringParser->parseRuleItems($ruleString);

        $this->runPresenceRules($state, $ruleTarget, $ruleItems, $targetValueContext);
    }

    public function validateLocalValue(ValidationState $state, $rawFieldKey, $ruleString)
    {
        $ruleTarget = $this->ruleStringParser->parseTargetKey($rawFieldKey);
        $targetValueContext = $state->targetValueContextStore()->get($ruleTarget->fieldPath());
        if (!$targetValueContext instanceof TargetValueContext || !$targetValueContext->isMaterialized() || $targetValueContext->isFailed()) {
            return;
        }

        $ruleItems = $this->ruleStringParser->parseRuleItems($ruleString);

        if ($targetValueContext->shouldSkipValueValidation()) {
            $this->finalizeAfterLocalStage($state, $ruleTarget, $ruleItems, $targetValueContext);

            return;
        }

        if (!$targetValueContext->currentExists()) {
            return;
        }

        if (!$this->runLocalValueRules($state, $ruleTarget, $ruleItems, $targetValueContext)) {
            return;
        }

        $this->finalizeAfterLocalStage($state, $ruleTarget, $ruleItems, $targetValueContext);
    }

    public function validateDependentValue(ValidationState $state, $rawFieldKey, $ruleString)
    {
        $ruleTarget = $this->ruleStringParser->parseTargetKey($rawFieldKey);
        $targetValueContext = $state->targetValueContextStore()->get($ruleTarget->fieldPath());
        if (
            !$targetValueContext instanceof TargetValueContext
            || !$targetValueContext->isMaterialized()
            || !$targetValueContext->isDependentReadable()
            || $targetValueContext->isFailed()
            || $targetValueContext->shouldSkipValueValidation()
        ) {
            return;
        }

        $ruleItems = $this->ruleStringParser->parseRuleItems($ruleString);
        if (!$this->hasDependentValueRules($ruleItems)) {
            return;
        }

        if (!$this->runDependentValueRules($state, $ruleTarget, $ruleItems, $targetValueContext)) {
            return;
        }

        $targetValueContext->commitOutputValue($state->normalizeOutput());
        $state->writeValidatedField(
            $ruleTarget->fieldPath(),
            $targetValueContext->outputValue()
        );
    }

    private function runMaterializationRules(ValidationState $state, RuleTarget $ruleTarget, array $ruleItems, TargetValueContext $targetValueContext)
    {
        foreach ($ruleItems as $ruleItem) {
            if (!$this->shouldRunInMaterializationPhase($ruleItem)) {
                continue;
            }

            $presenceResult = $this->executeRule($state, $ruleTarget, $ruleItem, $targetValueContext, ValidationPhase::PRESENCE);
            if ($presenceResult === false) {
                return false;
            }
            if ($presenceResult === null) {
                break;
            }

            $valueResult = $this->executeRule($state, $ruleTarget, $ruleItem, $targetValueContext, ValidationPhase::VALUE);
            if ($valueResult === false) {
                return false;
            }
            if ($valueResult === null) {
                break;
            }
        }

        return true;
    }

    private function runConditionalPresenceRules(ValidationState $state, RuleTarget $ruleTarget, array $ruleItems, TargetValueContext $targetValueContext)
    {
        foreach ($ruleItems as $ruleItem) {
            if (!$this->shouldRunInConditionalPresencePhase($ruleItem)) {
                continue;
            }

            $executionResult = $this->executeRule($state, $ruleTarget, $ruleItem, $targetValueContext, ValidationPhase::PRESENCE);
            if ($executionResult === false) {
                return false;
            }
            if ($executionResult === null) {
                break;
            }
        }

        return true;
    }

    private function runPresenceRules(ValidationState $state, RuleTarget $ruleTarget, array $ruleItems, TargetValueContext $targetValueContext)
    {
        foreach ($ruleItems as $ruleItem) {
            if (!$this->shouldRunInPresencePhase($ruleItem)) {
                continue;
            }

            $executionResult = $this->executeRule($state, $ruleTarget, $ruleItem, $targetValueContext, ValidationPhase::PRESENCE);
            if ($executionResult === false) {
                return false;
            }
            if ($executionResult === null) {
                break;
            }
        }

        return true;
    }

    private function runLocalValueRules(ValidationState $state, RuleTarget $ruleTarget, array $ruleItems, TargetValueContext $targetValueContext)
    {
        foreach ($ruleItems as $ruleItem) {
            if (!$this->shouldRunInLocalValuePhase($ruleItem)) {
                continue;
            }

            $executionResult = $this->executeRule($state, $ruleTarget, $ruleItem, $targetValueContext, ValidationPhase::VALUE);
            if ($executionResult === false) {
                return false;
            }
            if ($executionResult === null) {
                break;
            }
        }

        return true;
    }

    private function runDependentValueRules(ValidationState $state, RuleTarget $ruleTarget, array $ruleItems, TargetValueContext $targetValueContext)
    {
        foreach ($ruleItems as $ruleItem) {
            if (!$this->shouldRunInDependentValuePhase($ruleItem)) {
                continue;
            }

            $executionResult = $this->executeRule($state, $ruleTarget, $ruleItem, $targetValueContext, ValidationPhase::VALUE);
            if ($executionResult === false) {
                return false;
            }
            if ($executionResult === null) {
                break;
            }
        }

        return true;
    }

    private function executeRule(
        ValidationState $state,
        RuleTarget $ruleTarget,
        ParsedRuleToken $ruleItem,
        TargetValueContext $targetValueContext,
        $phase
    ) {
        $outcome = $this->ruleExecutor->execute(
            $phase,
            $state,
            $ruleTarget,
            $ruleItem,
            $targetValueContext
        );

        if ($outcome->isSkipped()) {
            return true;
        }

        if ($outcome->isFailed()) {
            $targetValueContext->markFailed();
            $state->addTargetFailure($ruleTarget, $ruleItem, $targetValueContext, $outcome);

            return false;
        }

        $targetValueContext->applyRuleResult($outcome->ruleResult());
        $resolvedRule = $outcome->resolvedRule();
        if (
            $phase === ValidationPhase::PRESENCE
            && $outcome->shouldBreak()
            && $resolvedRule !== null
            && is_subclass_of($resolvedRule->ruleClass(), ConditionalPresenceRuleInterface::class)
        ) {
            $targetValueContext->skipValueValidation();
        }

        return $outcome->shouldBreak() ? null : true;
    }

    private function shouldRunInMaterializationPhase($ruleItem)
    {
        $resolvedRule = $this->ruleSet->resolveRule($ruleItem->inputRuleKey());
        if ($resolvedRule === null) {
            return true;
        }

        return is_subclass_of($resolvedRule->ruleClass(), ValueMaterializationRuleInterface::class);
    }

    private function shouldRunInPresencePhase($ruleItem)
    {
        $resolvedRule = $this->ruleSet->resolveRule($ruleItem->inputRuleKey());
        if ($resolvedRule === null) {
            return true;
        }

        $ruleClass = $resolvedRule->ruleClass();

        return is_subclass_of($ruleClass, PresenceRuleInterface::class)
            && !is_subclass_of($ruleClass, ValueMaterializationRuleInterface::class)
            && !is_subclass_of($ruleClass, ConditionalPresenceRuleInterface::class);
    }

    private function shouldRunInConditionalPresencePhase($ruleItem)
    {
        $resolvedRule = $this->ruleSet->resolveRule($ruleItem->inputRuleKey());
        if ($resolvedRule === null) {
            return true;
        }

        $ruleClass = $resolvedRule->ruleClass();

        return is_subclass_of($ruleClass, PresenceRuleInterface::class)
            && is_subclass_of($ruleClass, ConditionalPresenceRuleInterface::class)
            && !is_subclass_of($ruleClass, ValueMaterializationRuleInterface::class);
    }

    private function shouldRunInLocalValuePhase($ruleItem)
    {
        $resolvedRule = $this->ruleSet->resolveRule($ruleItem->inputRuleKey());
        if ($resolvedRule === null) {
            return true;
        }

        $ruleClass = $resolvedRule->ruleClass();

        return !is_subclass_of($ruleClass, PresenceRuleInterface::class)
            && !is_subclass_of($ruleClass, ValueMaterializationRuleInterface::class)
            && !is_subclass_of($ruleClass, DependentValueRuleInterface::class);
    }

    private function shouldRunInDependentValuePhase($ruleItem)
    {
        $resolvedRule = $this->ruleSet->resolveRule($ruleItem->inputRuleKey());
        if ($resolvedRule === null) {
            return true;
        }

        return is_subclass_of($resolvedRule->ruleClass(), DependentValueRuleInterface::class);
    }

    private function hasDependentValueRules(array $ruleItems)
    {
        foreach ($ruleItems as $ruleItem) {
            if ($this->shouldRunInDependentValuePhase($ruleItem)) {
                return true;
            }
        }

        return false;
    }

    private function finalizeAfterLocalStage(ValidationState $state, RuleTarget $ruleTarget, array $ruleItems, TargetValueContext $targetValueContext)
    {
        $targetValueContext->markDependentReadable();
        $targetValueContext->commitOutputValue($state->normalizeOutput());

        if (!$this->hasDependentValueRules($ruleItems)) {
            $state->writeValidatedField(
                $ruleTarget->fieldPath(),
                $targetValueContext->outputValue()
            );
        }
    }
}
