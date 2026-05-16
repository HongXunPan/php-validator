<?php

namespace HongXunPan\Validator\Context;

class PathLabelMap
{
    /**
     * @var array
     */
    private $displayNameMap = array();

    public function register($fieldPath, $displayName)
    {
        $this->displayNameMap[(string)$fieldPath] = (string)$displayName;
    }

    public function resolve($fieldPath, $defaultValue = null)
    {
        $fieldPath = (string)$fieldPath;
        if (array_key_exists($fieldPath, $this->displayNameMap)) {
            return $this->displayNameMap[$fieldPath];
        }

        if ($defaultValue !== null && $defaultValue !== '') {
            return (string)$defaultValue;
        }

        return $fieldPath;
    }
}
