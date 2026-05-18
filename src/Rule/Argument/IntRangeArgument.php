<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class IntRangeArgument
{
    /**
     * @var int
     */
    private $min;
    /**
     * @var int
     */
    private $max;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct($min, $max)
    {
        $min = (int)$min;
        $max = (int)$max;
        if ($min > $max) {
            throw new InvalidRuleArgumentException('整数范围参数的 min 不能大于 max');
        }

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return int
     */
    public function min()
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function max()
    {
        return $this->max;
    }
}
