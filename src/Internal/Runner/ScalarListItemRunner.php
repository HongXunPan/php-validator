<?php

namespace HongXunPan\Validator\Internal\Runner;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Internal\Output\ScalarValidationOutput;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Plan\TargetRulePlanCompiler;
use HongXunPan\Validator\Internal\State\ValidationState;
use HongXunPan\Validator\Internal\Target\TargetPlanExecutor;

class ScalarListItemRunner
{
    const ITEM_PATH = '__item__';

    /**
     * @var TargetRulePlanCompiler
     */
    private $targetRulePlanCompiler;
    /**
     * @var TargetPlanExecutor
     */
    private $targetPlanExecutor;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;

    /**
     * @param TargetRulePlanCompiler $targetRulePlanCompiler
     * @param TargetPlanExecutor $targetPlanExecutor
     * @param PathAccessor $pathAccessor
     */
    public function __construct(
        TargetRulePlanCompiler $targetRulePlanCompiler,
        TargetPlanExecutor $targetPlanExecutor,
        PathAccessor $pathAccessor
    ) {
        $this->targetRulePlanCompiler = $targetRulePlanCompiler;
        $this->targetPlanExecutor = $targetPlanExecutor;
        $this->pathAccessor = $pathAccessor;
    }

    /**
     * @param mixed $value
     * @param string $ruleString
     * @param string $displayName
     *
     * @return ScalarValidationOutput
     */
    public function runOutput($value, $ruleString, $displayName)
    {
        $targetPlan = $this->targetRulePlanCompiler->compileStandalone(
            self::ITEM_PATH,
            $displayName,
            $ruleString
        );

        $pathLabelMap = new PathLabelMap();
        $pathLabelMap->register(self::ITEM_PATH, $displayName);

        $state = new ValidationState(
            array(self::ITEM_PATH => $value),
            ValidationOptions::forScalarListItem(),
            true,
            $this->pathAccessor,
            $pathLabelMap
        );

        $this->targetPlanExecutor->materialize($state, $targetPlan);
        $this->targetPlanExecutor->validateConditionalPresence($state, $targetPlan);
        $this->targetPlanExecutor->validatePresence($state, $targetPlan);
        $this->targetPlanExecutor->validateLocalValue($state, $targetPlan);
        $this->targetPlanExecutor->validateDependentValue($state, $targetPlan);

        return new ScalarValidationOutput($state->output(), self::ITEM_PATH);
    }
}
