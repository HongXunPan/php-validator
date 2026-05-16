<?php

namespace HongXunPan\Validator\Internal\Plan;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Internal\Input\DeclaredTargetTree;

class CompiledValidationPlan
{
    /**
     * @var CompiledTargetRulePlan[]
     */
    private $targetPlans;
    /**
     * @var PathLabelMap
     */
    private $pathLabelMap;
    /**
     * @var DeclaredTargetTree
     */
    private $declaredTargetTree;

    /**
     * @param array<int, CompiledTargetRulePlan> $targetPlans
     * @param PathLabelMap $pathLabelMap
     * @param DeclaredTargetTree $declaredTargetTree
     */
    public function __construct(array $targetPlans, PathLabelMap $pathLabelMap, DeclaredTargetTree $declaredTargetTree)
    {
        $this->targetPlans = $targetPlans;
        $this->pathLabelMap = $pathLabelMap;
        $this->declaredTargetTree = $declaredTargetTree;
    }

    /**
     * @return array<int, CompiledTargetRulePlan>
     */
    public function targetPlans()
    {
        return $this->targetPlans;
    }

    /**
     * @return PathLabelMap
     */
    public function pathLabelMap()
    {
        return $this->pathLabelMap;
    }

    /**
     * @return DeclaredTargetTree
     */
    public function declaredTargetTree()
    {
        return $this->declaredTargetTree;
    }
}
