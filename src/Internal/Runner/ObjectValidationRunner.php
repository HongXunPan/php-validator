<?php

namespace HongXunPan\Validator\Internal\Runner;

use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Internal\Execution\PhaseRuleRunner;
use HongXunPan\Validator\Internal\Execution\RuleExecutor;
use HongXunPan\Validator\Internal\Input\DeclaredTargetTreeBuilder;
use HongXunPan\Validator\Internal\Input\UnknownTargetDetector;
use HongXunPan\Validator\Internal\Output\ValidationOutput;
use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Plan\TargetRulePlanCompiler;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Internal\Target\TargetLifecycleManager;
use HongXunPan\Validator\Internal\Target\TargetPlanExecutor;
use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Support\RuleResultNormalizer;

class ObjectValidationRunner
{
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var TargetRulePlanCompiler
     */
    private $targetRulePlanCompiler;
    /**
     * @var TargetPlanExecutor
     */
    private $targetPlanExecutor;
    /**
     * @var UnknownTargetDetector
     */
    private $unknownTargetDetector;

    /**
     * @param RuleSet $ruleSet
     */
    public function __construct(RuleSet $ruleSet)
    {
        $this->pathAccessor = new PathAccessor();
        $ruleStringParser = new RuleStringParser();
        $this->targetRulePlanCompiler = new TargetRulePlanCompiler(
            $ruleStringParser,
            $ruleSet,
            new DeclaredTargetTreeBuilder()
        );
        $this->targetPlanExecutor = new TargetPlanExecutor(
            new PhaseRuleRunner(
                new RuleExecutor(
                    $ruleSet,
                    new LiteralValueParser(),
                    new RuleResultNormalizer()
                )
            ),
            new TargetLifecycleManager()
        );
        $this->unknownTargetDetector = new UnknownTargetDetector($this->pathAccessor);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @param ValidationOptions $options
     * @param bool $normalizeOutput
     *
     * @return \HongXunPan\Validator\Result\ValidationResult
     */
    public function run(array $data, array $rules, ValidationOptions $options, $normalizeOutput)
    {
        return $this->runOutput($data, $rules, $options, $normalizeOutput)->toValidationResult();
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @param ValidationOptions $options
     * @param bool $normalizeOutput
     *
     * @return ValidationOutput
     */
    public function runOutput(array $data, array $rules, ValidationOptions $options, $normalizeOutput)
    {
        $compiledPlan = $this->targetRulePlanCompiler->compile($rules);
        $state = new ValidationState(
            $data,
            $options,
            $normalizeOutput,
            $this->pathAccessor,
            $compiledPlan->pathLabelMap()
        );

        if ($options->rejectUnknown()) {
            $unknownDetailItems = $this->unknownTargetDetector->collect(
                $data,
                $compiledPlan->declaredTargetTree(),
                $options->fieldPrefix()
            );

            foreach ($unknownDetailItems as $unknownDetailItem) {
                $state->addUnknownDetailItem($unknownDetailItem);
            }
        }

        foreach ($compiledPlan->targetPlans() as $targetPlan) {
            $this->targetPlanExecutor->materialize($state, $targetPlan);
        }

        foreach ($compiledPlan->targetPlans() as $targetPlan) {
            $this->targetPlanExecutor->validateConditionalPresence($state, $targetPlan);
        }

        foreach ($compiledPlan->targetPlans() as $targetPlan) {
            $this->targetPlanExecutor->validatePresence($state, $targetPlan);
        }

        foreach ($compiledPlan->targetPlans() as $targetPlan) {
            $this->targetPlanExecutor->validateLocalValue($state, $targetPlan);
        }

        foreach ($compiledPlan->targetPlans() as $targetPlan) {
            $this->targetPlanExecutor->validateDependentValue($state, $targetPlan);
        }

        return $state->output();
    }

    /**
     * @return TargetRulePlanCompiler
     */
    public function targetRulePlanCompiler()
    {
        return $this->targetRulePlanCompiler;
    }

    /**
     * @return TargetPlanExecutor
     */
    public function targetPlanExecutor()
    {
        return $this->targetPlanExecutor;
    }

    /**
     * @return PathAccessor
     */
    public function pathAccessor()
    {
        return $this->pathAccessor;
    }
}
