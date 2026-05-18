<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class NumericRangeArgument
{
    /**
     * @var int|float
     */
    private $min;
    /**
     * @var int|float
     */
    private $max;

    /**
     * @param int|float $min
     * @param int|float $max
     */
    public function __construct($min, $max)
    {
        if ($min > $max) {
            throw new InvalidRuleArgumentException('数值范围参数的 min 不能大于 max');
        }

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return int|float
     */
    public function min()
    {
        return $this->min;
    }

    /**
     * @return int|float
     */
    public function max()
    {
        return $this->max;
    }
}
