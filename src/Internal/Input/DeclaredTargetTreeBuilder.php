<?php

namespace HongXunPan\Validator\Internal\Input;

use HongXunPan\Validator\Internal\Plan\CompiledTargetRulePlan;

class DeclaredTargetTreeBuilder
{
    public function build(array $targetPlans)
    {
        $tree = array();

        foreach ($targetPlans as $targetPlan) {
            if (!$targetPlan instanceof CompiledTargetRulePlan) {
                continue;
            }

            $segments = explode('.', $targetPlan->ruleTarget()->fieldPath());
            $current = &$tree;

            foreach ($segments as $segment) {
                if (!isset($current[$segment]) || !is_array($current[$segment])) {
                    $current[$segment] = array();
                }

                $current = &$current[$segment];
            }

            $current['__leaf'] = true;
            unset($current);
        }

        return new DeclaredTargetTree($tree);
    }
}
