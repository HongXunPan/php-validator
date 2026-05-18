<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class PositiveNumericArgument
{
    /**
     * @var int|float
     */
    private $value;

    /**
     * @param int|float $value
     */
    public function __construct($value)
    {
        if (!is_int($value) && !is_float($value)) {
            throw new InvalidRuleArgumentException('正数参数必须是 JSON number literal');
        }

        if ($value <= 0) {
            throw new InvalidRuleArgumentException('正数参数必须大于 0');
        }

        $this->value = $value;
    }

    /**
     * @return int|float
     */
    public function value()
    {
        return $this->value;
    }
}
