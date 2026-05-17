<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class FieldReferenceArgument
{
    /**
     * @var string
     */
    private $fieldPath;

    /**
     * @param string $fieldPath
     */
    public function __construct($fieldPath)
    {
        $fieldPath = trim((string)$fieldPath);
        if ($fieldPath === '') {
            throw new InvalidRuleArgumentException('字段引用参数不能为空');
        }

        $this->fieldPath = $fieldPath;
    }

    /**
     * @return string
     */
    public function fieldPath()
    {
        return $this->fieldPath;
    }
}
