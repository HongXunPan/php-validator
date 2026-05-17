<?php

namespace HongXunPan\Validator\Internal\State;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Context\TargetValueContextStore;
use HongXunPan\Validator\Internal\Context\TargetValueReader;
use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Execution\RuleExecutionOutcome;
use HongXunPan\Validator\Internal\Input\RawInputSource;
use HongXunPan\Validator\Internal\Output\ValidationFailureReporter;
use HongXunPan\Validator\Internal\Output\ValidationMessageRenderer;
use HongXunPan\Validator\Internal\Output\ValidationOutput;
use HongXunPan\Validator\Internal\Plan\CompiledRule;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Target\RuleTarget;

class ValidationState
{
    /**
     * @var ValidationOptions
     */
    private $options;
    /**
     * @var bool
     */
    private $normalizeOutput;
    /**
     * @var TargetValueContextStore
     */
    private $targetValueContextStore;
    /**
     * @var TargetValueReader
     */
    private $targetValueReader;
    /**
     * @var ValidationOutput
     */
    private $output;
    /**
     * @var ValidationFailureReporter
     */
    private $failureReporter;

    /**
     * @param array<string, mixed> $rawData
     * @param ValidationOptions $options
     * @param bool $normalizeOutput
     * @param PathAccessor $pathAccessor
     * @param PathLabelMap $pathLabelMap
     */
    public function __construct(
        array $rawData,
        ValidationOptions $options,
        $normalizeOutput,
        PathAccessor $pathAccessor,
        PathLabelMap $pathLabelMap
    ) {
        $this->options = $options;
        $this->normalizeOutput = (bool)$normalizeOutput;
        $this->targetValueContextStore = new TargetValueContextStore();
        $this->targetValueReader = new TargetValueReader(
            new RawInputSource($rawData, $pathAccessor),
            $this->targetValueContextStore
        );
        $this->output = new ValidationOutput($pathAccessor);
        $this->failureReporter = new ValidationFailureReporter(
            $this->output,
            new ValidationMessageRenderer(),
            $pathAccessor,
            $pathLabelMap,
            $options->fieldPrefix()
        );
    }

    /**
     * @return ValidationOptions
     */
    public function options()
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function strict()
    {
        return $this->options->strict();
    }

    /**
     * @return string
     */
    public function fieldPrefix()
    {
        return $this->options->fieldPrefix();
    }

    /**
     * @return bool
     */
    public function normalizeOutput()
    {
        return $this->normalizeOutput;
    }

    /**
     * @param string $targetPath
     * @param TargetValueContext $targetValueContext
     */
    public function rememberTargetValueContext($targetPath, TargetValueContext $targetValueContext)
    {
        $this->targetValueContextStore->remember($targetPath, $targetValueContext);
    }

    /**
     * @return TargetValueContextStore
     */
    public function targetValueContextStore()
    {
        return $this->targetValueContextStore;
    }

    /**
     * @return TargetValueReader
     */
    public function targetValueReader()
    {
        return $this->targetValueReader;
    }

    /**
     * @param ValidationDetailItem $detailItem
     */
    public function addUnknownDetailItem(ValidationDetailItem $detailItem)
    {
        $this->failureReporter->reportUnknownDetailItem($detailItem);
    }

    /**
     * @param RuleTarget $ruleTarget
     * @param TargetValueContext $targetValueContext
     */
    public function writeValidatedTarget(RuleTarget $ruleTarget, TargetValueContext $targetValueContext)
    {
        $this->output->writeValidatedTarget($ruleTarget, $targetValueContext);
    }

    /**
     * @param RuleTarget $ruleTarget
     * @param CompiledRule $compiledRule
     * @param TargetValueContext $targetValueContext
     * @param RuleExecutionOutcome $outcome
     */
    public function addTargetFailure(RuleTarget $ruleTarget, CompiledRule $compiledRule, TargetValueContext $targetValueContext, RuleExecutionOutcome $outcome)
    {
        $this->failureReporter->reportTargetFailure($ruleTarget, $compiledRule, $targetValueContext, $outcome);
    }

    /**
     * @param RuleTarget $ruleTarget
     *
     * @return string
     */
    public function displayName(RuleTarget $ruleTarget)
    {
        return $this->failureReporter->displayName($ruleTarget);
    }

    /**
     * @return \HongXunPan\Validator\Result\ValidationResult
     */
    public function toValidationResult()
    {
        return $this->output->toValidationResult();
    }

    /**
     * @return ValidationOutput
     */
    public function output()
    {
        return $this->output;
    }
}
