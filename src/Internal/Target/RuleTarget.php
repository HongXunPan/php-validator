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

    public function __construct($fieldPath, $displayName)
    {
        $this->fieldPath = (string)$fieldPath;
        $this->displayName = (string)$displayName;
    }

    public function fieldPath()
    {
        return $this->fieldPath;
    }

    public function displayName()
    {
        return $this->displayName;
    }
}
