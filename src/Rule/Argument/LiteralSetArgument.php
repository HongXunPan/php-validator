<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class LiteralSetArgument
{
    /**
     * @var array<int, mixed>
     */
    private $values;

    /**
     * @param array<int, mixed> $values
     */
    public function __construct(array $values)
    {
        if (empty($values)) {
            throw new InvalidRuleArgumentException('literal set 参数不能为空');
        }

        foreach ($values as $value) {
            if (is_array($value) || is_object($value)) {
                throw new InvalidRuleArgumentException('literal set 参数只允许标量或 null 成员');
            }
        }

        $this->values = array_values($values);
    }

    /**
     * @return array<int, mixed>
     */
    public function values()
    {
        return $this->values;
    }
}
