<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class StringSetArgument
{
    /**
     * @var array<int, string>
     */
    private $values;

    /**
     * @param array<int, string> $values
     */
    public function __construct(array $values)
    {
        if (empty($values)) {
            throw new InvalidRuleArgumentException('字符串集合参数不能为空');
        }

        foreach ($values as $value) {
            if (!is_string($value) || $value === '') {
                throw new InvalidRuleArgumentException('字符串集合参数只允许非空字符串成员');
            }
        }

        $this->values = array_values($values);
    }

    /**
     * @return array<int, string>
     */
    public function values()
    {
        return $this->values;
    }
}
