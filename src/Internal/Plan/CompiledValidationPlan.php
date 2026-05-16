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

    public function __construct(array $targetPlans, PathLabelMap $pathLabelMap, DeclaredTargetTree $declaredTargetTree)
    {
        $this->targetPlans = $targetPlans;
        $this->pathLabelMap = $pathLabelMap;
        $this->declaredTargetTree = $declaredTargetTree;
    }

    public function targetPlans()
    {
        return $this->targetPlans;
    }

    public function pathLabelMap()
    {
        return $this->pathLabelMap;
    }

    public function declaredTargetTree()
    {
        return $this->declaredTargetTree;
    }
}
