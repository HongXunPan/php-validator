<?php

namespace HongXunPan\Validator\Internal\Target;

use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Internal\Path\PathAccessor;

class UnknownTargetCollector
{
    /**
     * @var RuleStringParser
     */
    private $ruleStringParser;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;

    public function __construct(RuleStringParser $ruleStringParser, PathAccessor $pathAccessor)
    {
        $this->ruleStringParser = $ruleStringParser;
        $this->pathAccessor = $pathAccessor;
    }

    public function collect(array $data, array $rules, $fieldPrefix)
    {
        $detail = array();
        $allowedRuleTree = $this->buildAllowedRuleTree($rules);

        $this->collectRecursive(
            $data,
            $allowedRuleTree,
            (string)$fieldPrefix,
            $detail
        );

        return $detail;
    }

    private function buildAllowedRuleTree(array $rules)
    {
        $tree = array();

        foreach ($rules as $rawFieldKey => $ruleString) {
            $ruleTarget = $this->ruleStringParser->parseTargetKey($rawFieldKey);
            $segments = explode('.', $ruleTarget->fieldPath());
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

        return $tree;
    }

    private function collectRecursive(array $data, array $allowedRuleTree, $fieldPrefix, array &$detail)
    {
        foreach ($data as $key => $value) {
            $key = (string)$key;
            if (!array_key_exists($key, $allowedRuleTree)) {
                $displayName = $this->pathAccessor->buildDisplayName($key, $fieldPrefix);
                $detail[] = ValidationDetailItem::unknownField($displayName, $value)->toArray();

                continue;
            }

            $children = $this->extractChildren($allowedRuleTree[$key]);
            if (!$children || !is_array($value)) {
                continue;
            }

            $this->collectRecursive(
                $value,
                $children,
                $this->pathAccessor->buildDisplayName($key, $fieldPrefix),
                $detail
            );
        }
    }

    private function extractChildren(array $allowedRuleTreeNode)
    {
        $children = array();

        foreach ($allowedRuleTreeNode as $key => $value) {
            if ($key === '__leaf') {
                continue;
            }

            $children[$key] = $value;
        }

        return $children;
    }
}
