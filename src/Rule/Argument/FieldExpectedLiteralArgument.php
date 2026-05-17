<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class FieldExpectedLiteralArgument
{
    /**
     * @var string
     */
    private $fieldPath;
    /**
     * @var mixed
     */
    private $expectedValue;

    /**
     * @param string $fieldPath
     * @param mixed $expectedValue
     */
    public function __construct($fieldPath, $expectedValue)
    {
        $fieldPath = trim((string)$fieldPath);
        if ($fieldPath === '') {
            throw new InvalidRuleArgumentException('字段等值参数的字段路径不能为空');
        }

        if (is_array($expectedValue) || is_object($expectedValue)) {
            throw new InvalidRuleArgumentException('字段等值参数只允许标量或 null literal');
        }

        $this->fieldPath = $fieldPath;
        $this->expectedValue = $expectedValue;
    }

    /**
     * @return string
     */
    public function fieldPath()
    {
        return $this->fieldPath;
    }

    /**
     * @return mixed
     */
    public function expectedValue()
    {
        return $this->expectedValue;
    }
}
