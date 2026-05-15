<?php

namespace HongXunPan\Validator\Internal\Runner;

use HongXunPan\Validator\Internal\Field\PathLabelMap;
use HongXunPan\Validator\Internal\Field\TargetValidator;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Support\PathAccessor;
use HongXunPan\Validator\Support\RuleParser;
use HongXunPan\Validator\Support\UnknownFieldCollector;

class ObjectValidationRunner
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
     * @var TargetValidator
     */
    private $fieldValidator;
    /**
     * @var UnknownFieldCollector
     */
    private $unknownFieldCollector;

    public function __construct(RuleSet $ruleSet)
    {
        $this->pathAccessor = new PathAccessor();
        $this->ruleParser = new RuleParser();
        $this->fieldValidator = new TargetValidator($ruleSet, $this->ruleParser, $this->pathAccessor);
        $this->unknownFieldCollector = new UnknownFieldCollector($this->ruleParser, $this->pathAccessor);
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
            $unknownDetails = $this->unknownFieldCollector->collect(
                $data,
                $rules,
                $options->fieldPrefix()
            );

            foreach ($unknownDetails as $unknownDetail) {
                $state->addUnknownField($unknownDetail['param'], $unknownDetail['value']);
            }
        }

        foreach ($rules as $rawFieldKey => $ruleString) {
            $this->fieldValidator->validate($state, $rawFieldKey, $ruleString);
        }

        return $state->toValidationResult();
    }

    private function buildPathLabelMap(array $rules)
    {
        $pathLabelMap = new PathLabelMap();

        foreach ($rules as $rawFieldKey => $ruleString) {
            $ruleTarget = $this->ruleParser->parseFieldRuleKey($rawFieldKey);
            $pathLabelMap->register($ruleTarget->fieldPath(), $ruleTarget->displayName());
        }

        return $pathLabelMap;
    }
}
