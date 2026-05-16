<?php

namespace HongXunPan\Validator\Context;

class PathLabelMap
{
    /**
     * @var array
     */
    private $displayNameMap = array();

    /**
     * @param string $fieldPath
     * @param string $displayName
     */
    public function register($fieldPath, $displayName)
    {
        $this->displayNameMap[(string)$fieldPath] = (string)$displayName;
    }

    /**
     * @param string $fieldPath
     * @param mixed $defaultValue
     *
     * @return string
     */
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
