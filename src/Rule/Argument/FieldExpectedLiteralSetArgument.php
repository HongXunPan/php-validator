<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class FieldExpectedLiteralSetArgument
{
    /**
     * @var string
     */
    private $fieldPath;
    /**
     * @var array<int, mixed>
     */
    private $expectedValues;

    /**
     * @param string $fieldPath
     * @param array<int, mixed> $expectedValues
     */
    public function __construct($fieldPath, array $expectedValues)
    {
        $fieldPath = trim((string)$fieldPath);
        if ($fieldPath === '') {
            throw new InvalidRuleArgumentException('字段集合参数的字段路径不能为空');
        }

        if (empty($expectedValues)) {
            throw new InvalidRuleArgumentException('字段集合参数的 expected values 不能为空');
        }

        foreach ($expectedValues as $expectedValue) {
            if (is_array($expectedValue) || is_object($expectedValue)) {
                throw new InvalidRuleArgumentException('字段集合参数只允许标量或 null literal 成员');
            }
        }

        $this->fieldPath = $fieldPath;
        $this->expectedValues = array_values($expectedValues);
    }

    /**
     * @return string
     */
    public function fieldPath()
    {
        return $this->fieldPath;
    }

    /**
     * @return array<int, mixed>
     */
    public function expectedValues()
    {
        return $this->expectedValues;
    }
}
