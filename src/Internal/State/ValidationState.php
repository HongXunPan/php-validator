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
use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
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

    public function options()
    {
        return $this->options;
    }

    public function strict()
    {
        return $this->options->strict();
    }

    public function fieldPrefix()
    {
        return $this->options->fieldPrefix();
    }

    public function normalizeOutput()
    {
        return $this->normalizeOutput;
    }

    public function rememberTargetValueContext($targetPath, TargetValueContext $targetValueContext)
    {
        $this->targetValueContextStore->remember($targetPath, $targetValueContext);
    }

    public function targetValueContextStore()
    {
        return $this->targetValueContextStore;
    }

    public function targetValueReader()
    {
        return $this->targetValueReader;
    }

    public function addUnknownDetailItem(ValidationDetailItem $detailItem)
    {
        $this->failureReporter->reportUnknownDetailItem($detailItem);
    }

    public function writeValidatedTarget(RuleTarget $ruleTarget, TargetValueContext $targetValueContext)
    {
        $this->output->writeValidatedTarget($ruleTarget, $targetValueContext);
    }

    public function addTargetFailure(RuleTarget $ruleTarget, ParsedRuleToken $parsedRule, TargetValueContext $targetValueContext, RuleExecutionOutcome $outcome)
    {
        $this->failureReporter->reportTargetFailure($ruleTarget, $parsedRule, $targetValueContext, $outcome);
    }

    public function displayName(RuleTarget $ruleTarget)
    {
        return $this->failureReporter->displayName($ruleTarget);
    }

    public function toValidationResult()
    {
        return $this->output->toValidationResult();
    }

    public function output()
    {
        return $this->output;
    }
}
