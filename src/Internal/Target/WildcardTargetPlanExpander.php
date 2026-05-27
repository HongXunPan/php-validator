<?php

namespace HongXunPan\Validator\Internal\Target;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Plan\CompiledTargetRulePlan;
use HongXunPan\Validator\Internal\Plan\CompiledValidationPlan;

class WildcardTargetPlanExpander
{
    const WILDCARD_SEGMENT = '*';

    /**
     * @var PathAccessor
     */
    private $pathAccessor;

    /**
     * @param PathAccessor $pathAccessor
     */
    public function __construct(PathAccessor $pathAccessor)
    {
        $this->pathAccessor = $pathAccessor;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return CompiledValidationPlan
     */
    public function expand(array $data, CompiledValidationPlan $compiledPlan)
    {
        $targetPlans = array();
        $pathLabelMap = new PathLabelMap();

        foreach ($compiledPlan->targetPlans() as $targetPlan) {
            if (!$targetPlan instanceof CompiledTargetRulePlan) {
                continue;
            }

            if (!$this->hasWildcardSegment($targetPlan->ruleTarget()->fieldPath())) {
                $targetPlans[] = $targetPlan;
                $pathLabelMap->register(
                    $targetPlan->ruleTarget()->fieldPath(),
                    $targetPlan->ruleTarget()->displayName()
                );
                continue;
            }

            foreach ($this->expandPath($data, $targetPlan->ruleTarget()->fieldPath()) as $fieldPath) {
                $targetPlans[] = $targetPlan->withRuleTarget(new RuleTarget($fieldPath, $fieldPath));
                $pathLabelMap->register($fieldPath, $fieldPath);
            }
        }

        return new CompiledValidationPlan(
            $targetPlans,
            $pathLabelMap,
            $compiledPlan->declaredTargetTree()
        );
    }

    /**
     * @param string $fieldPath
     *
     * @return bool
     */
    private function hasWildcardSegment($fieldPath)
    {
        foreach (explode('.', (string)$fieldPath) as $segment) {
            if ($segment === self::WILDCARD_SEGMENT) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $data
     * @param string $fieldPath
     *
     * @return array<int, string>
     */
    private function expandPath(array $data, $fieldPath)
    {
        $expandedPaths = array();
        $this->expandSegments(
            $data,
            true,
            explode('.', (string)$fieldPath),
            0,
            '',
            false,
            $expandedPaths
        );

        return $expandedPaths;
    }

    /**
     * @param mixed $currentValue
     * @param bool $currentExists
     * @param array<int, string> $segments
     * @param int $position
     * @param string $currentPath
     * @param bool $wildcardExpanded
     * @param array<int, string> $expandedPaths
     *
     * @return void
     */
    private function expandSegments($currentValue, $currentExists, array $segments, $position, $currentPath, $wildcardExpanded, array &$expandedPaths)
    {
        if ($position >= count($segments)) {
            if ($wildcardExpanded) {
                $expandedPaths[] = $currentPath;
            }

            return;
        }

        $segment = (string)$segments[$position];
        if ($segment === self::WILDCARD_SEGMENT) {
            if (!$currentExists || !is_array($currentValue)) {
                return;
            }

            foreach ($currentValue as $key => $value) {
                $this->expandSegments(
                    $value,
                    true,
                    $segments,
                    $position + 1,
                    $this->pathAccessor->join($currentPath, (string)$key),
                    true,
                    $expandedPaths
                );
            }

            return;
        }

        $nextExists = false;
        $nextValue = null;
        if ($currentExists && is_array($currentValue) && array_key_exists($segment, $currentValue)) {
            $nextExists = true;
            $nextValue = $currentValue[$segment];
        }

        $this->expandSegments(
            $nextValue,
            $nextExists,
            $segments,
            $position + 1,
            $this->pathAccessor->join($currentPath, $segment),
            $wildcardExpanded,
            $expandedPaths
        );
    }
}
