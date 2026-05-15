<?php

namespace HongXunPan\Validator\Internal\Field;

use HongXunPan\Validator\Internal\Execution\RuleExecutor;
use HongXunPan\Validator\Internal\Execution\ValidationPhase;
use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Support\PathAccessor;
use HongXunPan\Validator\Support\RuleParser;
use HongXunPan\Validator\Support\RuleResultNormalizer;

class TargetValidator
{
    /**
     * @var RuleParser
     */
    private $ruleParser;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var RuleExecutor
     */
    private $ruleExecutor;

    public function __construct(RuleSet $ruleSet, RuleParser $ruleParser, PathAccessor $pathAccessor)
    {
        $this->ruleParser = $ruleParser;
        $this->pathAccessor = $pathAccessor;
        $this->ruleExecutor = new RuleExecutor(
            $ruleSet,
            $pathAccessor,
            new LiteralValueParser(),
            new RuleResultNormalizer()
        );
    }

    public function validate(ValidationState $state, $rawFieldKey, $ruleString)
    {
        $ruleTarget = $this->ruleParser->parseFieldRuleKey($rawFieldKey);
        $ruleItems = $this->ruleParser->parseRuleItems($ruleString);
        $lookupResult = $this->pathAccessor->getValue(
            $state->rawData(),
            $ruleTarget->fieldPath(),
            $state->strict()
        );
        $targetState = new TargetState($lookupResult->exists(), $lookupResult->value());

        if (!$this->runPhase(ValidationPhase::PRESENCE, $state, $ruleTarget, $ruleItems, $targetState)) {
            return;
        }

        if (!$targetState->exists()) {
            return;
        }

        if (!$this->runPhase(ValidationPhase::VALUE, $state, $ruleTarget, $ruleItems, $targetState)) {
            return;
        }

        $state->writeValidatedField(
            $ruleTarget->fieldPath(),
            $targetState->outputValue($state->normalizeOutput())
        );
    }

    private function runPhase($phase, ValidationState $state, RuleTarget $ruleTarget, array $ruleItems, TargetState $targetState)
    {
        foreach ($ruleItems as $ruleItem) {
            $outcome = $this->ruleExecutor->execute(
                $phase,
                $state,
                $ruleTarget,
                $ruleItem,
                $targetState
            );

            if ($outcome->isSkipped()) {
                continue;
            }

            if ($outcome->isFailed()) {
                $state->addFieldFailure($ruleTarget, $ruleItem, $targetState, $outcome);

                return false;
            }

            $targetState->apply($outcome->ruleResult());

            if ($outcome->shouldBreak()) {
                break;
            }
        }

        return true;
    }
}
