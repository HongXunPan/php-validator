<?php

namespace HongXunPan\Validator\Support;

class UnknownFieldCollector
{
    private $ruleParser;
    private $pathAccessor;

    public function __construct(RuleParser $ruleParser, PathAccessor $pathAccessor)
    {
        $this->ruleParser = $ruleParser;
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
            $fieldInfo = $this->ruleParser->parseFieldRuleKey($rawFieldKey);
            $segments = explode('.', $fieldInfo['field']);
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
                $detail[] = array(
                    'param' => $displayName,
                    'value' => $value,
                    'rule' => 'unknown',
                    'rule_value' => '',
                    'reason' => 'unknown field',
                );

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
