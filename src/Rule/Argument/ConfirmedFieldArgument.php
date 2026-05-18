<?php

namespace HongXunPan\Validator\Rule\Argument;

class ConfirmedFieldArgument
{
    /**
     * @var string|null
     */
    private $fieldPath;

    /**
     * @param string|null $fieldPath
     */
    public function __construct($fieldPath)
    {
        $fieldPath = trim((string)$fieldPath);
        $this->fieldPath = $fieldPath === '' ? null : $fieldPath;
    }

    /**
     * @return string|null
     */
    public function fieldPath()
    {
        return $this->fieldPath;
    }
}
