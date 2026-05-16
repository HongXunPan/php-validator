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

    public function __construct(PathAccessor $pathAccessor)
    {
        $this->pathAccessor = $pathAccessor;
    }

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

    private function collectRecursive(array $data, array $allowedRuleTree, $fieldPrefix, array &$detailItems)
    {
        foreach ($data as $key => $value) {
            $key = (string)$key;
            if (!array_key_exists($key, $allowedRuleTree)) {
                $displayName = $this->pathAccessor->buildDisplayName($key, $fieldPrefix);
                $detailItems[] = ValidationDetailItem::unknownField($displayName, $value);

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
                $detailItems
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
