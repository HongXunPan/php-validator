<?php

namespace HongXunPan\Validator\Internal\Input;

use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Path\PathAccessor;

class UnknownTargetDetector
{
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
     * @param DeclaredTargetTree $declaredTargetTree
     * @param string $fieldPrefix
     *
     * @return array<int, ValidationDetailItem>
     */
    public function collect(array $data, DeclaredTargetTree $declaredTargetTree, $fieldPrefix)
    {
        $detailItems = array();

        $this->collectRecursive(
            $data,
            $declaredTargetTree->toArray(),
            (string)$fieldPrefix,
            $detailItems
        );

        return $detailItems;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $allowedRuleTree
     * @param string $fieldPrefix
     * @param array<int, ValidationDetailItem> $detailItems
     *
     * @return void
     */
    private function collectRecursive(array $data, array $allowedRuleTree, $fieldPrefix, array &$detailItems)
    {
        foreach ($data as $key => $value) {
            $key = (string)$key;
            $allowedRuleTreeNode = null;
            if (array_key_exists($key, $allowedRuleTree)) {
                $allowedRuleTreeNode = $allowedRuleTree[$key];
            } elseif (array_key_exists('*', $allowedRuleTree)) {
                $allowedRuleTreeNode = $allowedRuleTree['*'];
            }

            if (!is_array($allowedRuleTreeNode)) {
                $displayName = $this->pathAccessor->buildDisplayName($key, $fieldPrefix);
                $detailItems[] = ValidationDetailItem::unknownField($displayName, $value);

                continue;
            }

            $children = $this->extractChildren($allowedRuleTreeNode);
            if (!$children || !is_array($value)) {
                continue;
            }

            $this->collectRecursive(
                $value,
                $children,
                $this->pathAccessor->buildDisplayName($key, $fieldPrefix),
                $detailItems
            );
        }
    }

    /**
     * @param array<string, mixed> $allowedRuleTreeNode
     *
     * @return array<string, mixed>
     */
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
