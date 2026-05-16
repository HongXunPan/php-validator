<?php

namespace HongXunPan\Validator\Internal\Target;

class RuleTarget
{
    /**
     * @var string
     */
    private $fieldPath;
    /**
     * @var string
     */
    private $displayName;

    /**
     * @param string $fieldPath
     * @param string $displayName
     */
    public function __construct($fieldPath, $displayName)
    {
        $this->fieldPath = (string)$fieldPath;
        $this->displayName = (string)$displayName;
    }

    /**
     * @return string
     */
    public function fieldPath()
    {
        return $this->fieldPath;
    }

    /**
     * @return string
     */
    public function displayName()
    {
        return $this->displayName;
    }
}
