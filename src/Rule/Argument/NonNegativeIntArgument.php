<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class NonNegativeIntArgument
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct($value)
    {
        if (!is_int($value)) {
            throw new InvalidRuleArgumentException('非负整数参数必须是 JSON integer literal');
        }

        if ($value < 0) {
            throw new InvalidRuleArgumentException('非负整数参数不能小于 0');
        }

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function value()
    {
        return $this->value;
    }
}
