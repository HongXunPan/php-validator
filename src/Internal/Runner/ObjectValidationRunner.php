<?php

namespace HongXunPan\Validator\Internal\Runner;

use HongXunPan\Validator\Internal\Path\PathLabelMap;
use HongXunPan\Validator\Internal\Target\TargetValidator;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Internal\Target\UnknownTargetCollector;

class ObjectValidationRunner
{
    /**
     * @var RuleStringParser
     */
    private $ruleStringParser;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var TargetValidator
     */
    private $targetValidator;
    /**
     * @var UnknownTargetCollector
     */
    private $unknownTargetCollector;

    public function __construct(RuleSet $ruleSet)
    {
        $this->pathAccessor = new PathAccessor();
        $this->ruleStringParser = new RuleStringParser();
        $this->targetValidator = new TargetValidator($ruleSet, $this->ruleStringParser, $this->pathAccessor);
        $this->unknownTargetCollector = new UnknownTargetCollector($this->ruleStringParser, $this->pathAccessor);
    }

    public function run(array $data, array $rules, ValidationOptions $options, $normalizeOutput)
    {
        $state = new ValidationState(
            $data,
            $options,
            $normalizeOutput,
            $this->pathAccessor,
            $this->buildPathLabelMap($rules)
        );

        if ($options->rejectUnknown()) {
            $unknownDetails = $this->unknownTargetCollector->collect(
                $data,
                $rules,
                $options->fieldPrefix()
            );

            foreach ($unknownDetails as $unknownDetail) {
                $state->addUnknownField($unknownDetail['param'], $unknownDetail['value']);
            }
        }

        foreach ($rules as $rawFieldKey => $ruleString) {
            $this->targetValidator->materialize($state, $rawFieldKey, $ruleString);
        }

        foreach ($rules as $rawFieldKey => $ruleString) {
            $this->targetValidator->validateConditionalPresence($state, $rawFieldKey, $ruleString);
        }

        foreach ($rules as $rawFieldKey => $ruleString) {
            $this->targetValidator->validatePresence($state, $rawFieldKey, $ruleString);
        }

        foreach ($rules as $rawFieldKey => $ruleString) {
            $this->targetValidator->validateLocalValue($state, $rawFieldKey, $ruleString);
        }

        foreach ($rules as $rawFieldKey => $ruleString) {
            $this->targetValidator->validateDependentValue($state, $rawFieldKey, $ruleString);
        }

        return $state->toValidationResult();
    }

    private function buildPathLabelMap(array $rules)
    {
        $pathLabelMap = new PathLabelMap();

        foreach ($rules as $rawFieldKey => $ruleString) {
            $ruleTarget = $this->ruleStringParser->parseTargetKey($rawFieldKey);
            $pathLabelMap->register($ruleTarget->fieldPath(), $ruleTarget->displayName());
        }

        return $pathLabelMap;
    }
}
